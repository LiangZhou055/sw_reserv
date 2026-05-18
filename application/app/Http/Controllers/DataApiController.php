<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\StoreContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataApiController extends Controller
{
    private const PERIOD_TODAY = 'today';
    private const PERIOD_7 = '7';
    private const PERIOD_14 = '14';
    private const PERIOD_30 = '30';

    public function index(Request $request, string $storeCode, string $apiKey): JsonResponse
    {
        $storeCode = strtolower(trim($storeCode));
        $apiKey = trim($apiKey);

        if ($storeCode === '' || $apiKey === '') {
            return response()->json(['error' => 'Missing store code or api key'], 400);
        }

        $store = $this->resolveAndAuthorizeStore($storeCode, $apiKey);
        if (! $store) {
            return response()->json(['error' => 'Invalid store or api key'], 404);
        }

        if (! StoreContext::applyForStore($storeCode)) {
            return response()->json(['error' => 'Store context apply failed'], 500);
        }

        $period = (string) $request->query('period', self::PERIOD_TODAY);
        $allowed = [self::PERIOD_TODAY, self::PERIOD_7, self::PERIOD_14, self::PERIOD_30];
        if (! in_array($period, $allowed, true)) {
            return response()->json([
                'error' => 'Invalid period',
                'allowed' => $allowed,
            ], 400);
        }

        if ($period === self::PERIOD_TODAY) {
            return response()->json($this->todayData($store));
        }

        return response()->json($this->periodData($store, (int) $period));
    }

    private function resolveAndAuthorizeStore(string $storeCode, string $apiKey): ?object
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [$storeCode])
            ->where('is_active', 1)
            ->first();

        if (! $store) {
            return null;
        }

        $hasDataApiKey = Schema::connection('central')->hasColumn('stores', 'data_api_key');
        if (! $hasDataApiKey) {
            return null;
        }

        if (empty($store->data_api_key)) {
            return null;
        }

        if (! hash_equals((string) $store->data_api_key, $apiKey)) {
            return null;
        }

        return $store;
    }

    private function todayData(object $store): array
    {
        $tz = config('app.timezone', 'America/New_York');
        $today = Carbon::today($tz)->toDateString();

        $events = Event::query()
            ->whereDate('start', $today)
            ->where('status', '!=', Event::STATUS_DELETE)
            ->orderBy('time')
            ->orderBy('id')
            ->get(['id', 'order_no', 'start', 'time', 'person_no', 'status', 'source', 'created_at']);

        return [
            'store_code' => (string) ($store->code ?? ''),
            'store_name' => (string) ($store->rest_name ?? $store->code ?? ''),
            'timezone' => $tz,
            'period' => self::PERIOD_TODAY,
            'date' => $today,
            'snapshot' => [
                'waiting' => $events->where('status', Event::STATUS_WAITING)->count(),
                'dine' => $events->where('status', Event::STATUS_DINE)->count(),
                'cancelled' => $events->where('status', Event::STATUS_CANCEL)->count(),
                'total' => $events->count(),
            ],
            'reservations' => $this->mapReservations($events),
        ];
    }

    private function periodData(object $store, int $days): array
    {
        $tz = config('app.timezone', 'America/New_York');
        $toDate = Carbon::today($tz)->toDateString();
        $fromDate = Carbon::today($tz)->subDays($days - 1)->toDateString();

        $events = Event::query()
            ->whereDate('start', '>=', $fromDate)
            ->whereDate('start', '<=', $toDate)
            ->where('status', '!=', Event::STATUS_DELETE)
            ->orderBy('start')
            ->orderBy('time')
            ->orderBy('id')
            ->get(['id', 'order_no', 'start', 'time', 'person_no', 'status', 'source', 'created_at']);

        $daily = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = Carbon::today($tz)->subDays($i)->toDateString();
            $dayEvents = $events->filter(static function (Event $event) use ($day) {
                return (string) $event->start === $day;
            });

            $daily[] = [
                'date' => $day,
                'total' => $dayEvents->count(),
                'waiting' => $dayEvents->where('status', Event::STATUS_WAITING)->count(),
                'dine' => $dayEvents->where('status', Event::STATUS_DINE)->count(),
                'cancelled' => $dayEvents->where('status', Event::STATUS_CANCEL)->count(),
            ];
        }

        return [
            'store_code' => (string) ($store->code ?? ''),
            'store_name' => (string) ($store->rest_name ?? $store->code ?? ''),
            'timezone' => $tz,
            'period' => (string) $days,
            'from' => $fromDate,
            'to' => $toDate,
            'summary' => [
                'total_reservations' => $events->count(),
                'total_waiting' => $events->where('status', Event::STATUS_WAITING)->count(),
                'total_dine' => $events->where('status', Event::STATUS_DINE)->count(),
                'total_cancelled' => $events->where('status', Event::STATUS_CANCEL)->count(),
            ],
            'daily' => $daily,
            'reservations' => $this->mapReservations($events),
        ];
    }

    private function mapReservations($events): array
    {
        $prefix = StoreContext::getRestPrefix();

        return $events->map(static function (Event $event) use ($prefix) {
            return [
                'id' => (int) $event->id,
                'order_no' => $prefix.(string) $event->order_no,
                'date' => (string) $event->start,
                'time' => substr((string) $event->time, 0, 5),
                'person_no' => (int) $event->person_no,
                'status' => (int) $event->status,
                'status_label' => Event::statusLabel($event->status),
                'source' => (int) ($event->source ?? Event::SOURCE_STORE),
                'source_label' => Event::sourceLabel((int) ($event->source ?? Event::SOURCE_STORE)),
                'created_at' => optional($event->created_at)->toIso8601String(),
            ];
        })->values()->all();
    }
}

