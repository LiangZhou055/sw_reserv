<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $stores = DB::connection('central')
            ->table('stores')
            ->orderBy('code')
            ->get();

        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $storeCode = strtolower(trim((string) $request->query('store', '')));
        if ($storeCode === '' && $stores->isNotEmpty()) {
            $storeCode = strtolower((string) $stores->first()->code);
        }

        if ($storeCode === 'all') {
            $rows = [];
            foreach ($stores as $store) {
                $rows[] = $this->summarizeStore($store, $startDate, $endDate);
            }

            return view('admin.reports.index', [
                'mode' => 'all',
                'stores' => $stores,
                'storeCode' => 'all',
                'startDate' => $startDate,
                'endDate' => $endDate,
                'rows' => $rows,
                'user' => auth('store')->user(),
            ]);
        }

        $store = $stores->first(function ($row) use ($storeCode) {
            return strtolower((string) $row->code) === $storeCode;
        });

        if (! $store) {
            abort(404, 'Store not found');
        }

        StoreContext::applyForStore($store->code);
        $report = $this->buildStoreReport($startDate, $endDate);

        return view('admin.reports.index', [
            'mode' => 'single',
            'stores' => $stores,
            'store' => $store,
            'storeCode' => strtolower((string) $store->code),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'report' => $report,
            'user' => auth('store')->user(),
        ]);
    }

    private function summarizeStore(object $store, string $startDate, string $endDate): array
    {
        $base = [
            'code' => $store->code,
            'name' => $store->rest_name ?? '',
            'error' => null,
            'reservations' => 0,
            'canceled' => 0,
            'customer_source' => 0,
            'sms_total' => 0,
            'sms_sent' => 0,
            'sms_failed' => 0,
            'sms_inbound' => 0,
        ];

        try {
            StoreContext::applyForStore($store->code);
            $eventBase = Event::query()
                ->whereDate('start', '>=', $startDate)
                ->whereDate('start', '<=', $endDate);

            $base['reservations'] = (clone $eventBase)->count();
            $base['canceled'] = (clone $eventBase)
                ->where('status', Event::STATUS_CANCEL)
                ->count();
            $base['customer_source'] = (clone $eventBase)
                ->where('source', Event::SOURCE_CUSTOMER)
                ->count();

            $sms = $this->smsStats($startDate, $endDate);
            $base['sms_total'] = $sms['total'];
            $base['sms_sent'] = $sms['sent'];
            $base['sms_failed'] = $sms['failed'];
            $base['sms_inbound'] = $sms['inbound'];
        } catch (\Throwable $e) {
            Log::warning('Admin report failed on store', ['store' => $store->code, 'error' => $e->getMessage()]);
            $base['error'] = $e->getMessage();
        }

        return $base;
    }

    private function buildStoreReport(string $startDate, string $endDate): array
    {
        $eventBase = Event::query()
            ->whereDate('start', '>=', $startDate)
            ->whereDate('start', '<=', $endDate);

        $statusCounts = (clone $eventBase)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $smsStatusCounts = (clone $eventBase)
            ->selectRaw('sms_status, COUNT(*) as cnt')
            ->groupBy('sms_status')
            ->pluck('cnt', 'sms_status')
            ->toArray();

        $sourceCounts = (clone $eventBase)
            ->selectRaw('source, COUNT(*) as cnt')
            ->groupBy('source')
            ->pluck('cnt', 'source')
            ->toArray();

        $dailyReservations = (clone $eventBase)
            ->selectRaw('DATE(start) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailyCanceled = (clone $eventBase)
            ->where('status', Event::STATUS_CANCEL)
            ->selectRaw('DATE(start) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailyLabels = $dailyReservations->pluck('day')->map(fn ($d) => (string) $d)->values()->all();
        $sms = $this->smsStats($startDate, $endDate);

        return [
            'reservations_total' => (clone $eventBase)->count(),
            'status_counts' => $statusCounts,
            'sms_status_counts' => $smsStatusCounts,
            'source_counts' => $sourceCounts,
            'daily_labels' => $dailyLabels,
            'daily_reservations' => $this->mapDailyToLabels($dailyLabels, $dailyReservations),
            'daily_canceled' => $this->mapDailyToLabels($dailyLabels, $dailyCanceled),
            'sms' => $sms,
        ];
    }

    private function smsStats(string $startDate, string $endDate): array
    {
        $table = $this->resolveSmsTable();
        $empty = [
            'table' => $table,
            'total' => 0,
            'sent' => 0,
            'failed' => 0,
            'inbound' => 0,
            'outbound' => 0,
            'daily_labels' => [],
            'daily_total' => [],
            'daily_sent' => [],
            'daily_failed' => [],
        ];

        if (! $table || ! Schema::hasColumn($table, 'initiated_time')) {
            return $empty;
        }

        $base = DB::table($table)
            ->whereDate('initiated_time', '>=', $startDate)
            ->whereDate('initiated_time', '<=', $endDate);

        $hasType = Schema::hasColumn($table, 'type');
        $hasStatus = Schema::hasColumn($table, 'status');

        $total = (clone $base)->count();
        $inbound = 0;
        $outbound = $total;
        if ($hasType) {
            $inbound = (clone $base)->where('type', 2)->count();
            $outbound = (clone $base)->where(function ($q) {
                $q->whereNull('type')->orWhere('type', '!=', 2);
            })->count();
        }

        $sent = 0;
        $failed = 0;
        if ($hasStatus) {
            $sent = (clone $base)->where('status', 1)->count();
            $failed = (clone $base)->where('status', 2)->count();
        }

        $dailyTotal = (clone $base)
            ->selectRaw('DATE(initiated_time) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailySent = collect();
        $dailyFailed = collect();
        if ($hasStatus) {
            $dailySent = (clone $base)
                ->where('status', 1)
                ->selectRaw('DATE(initiated_time) as day, COUNT(*) as cnt')
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            $dailyFailed = (clone $base)
                ->where('status', 2)
                ->selectRaw('DATE(initiated_time) as day, COUNT(*) as cnt')
                ->groupBy('day')
                ->orderBy('day')
                ->get();
        }

        $labels = $dailyTotal->pluck('day')->map(fn ($d) => (string) $d)->values()->all();

        return [
            'table' => $table,
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'inbound' => $inbound,
            'outbound' => $outbound,
            'daily_labels' => $labels,
            'daily_total' => $this->mapDailyToLabels($labels, $dailyTotal),
            'daily_sent' => $this->mapDailyToLabels($labels, $dailySent),
            'daily_failed' => $this->mapDailyToLabels($labels, $dailyFailed),
        ];
    }

    private function mapDailyToLabels(array $labels, $rows): array
    {
        $values = $rows->pluck('cnt', 'day')->toArray();
        $series = [];
        foreach ($labels as $label) {
            $series[] = (int) ($values[$label] ?? 0);
        }

        return $series;
    }

    private function resolveSmsTable(): ?string
    {
        if (Schema::hasTable('sms_logs')) {
            return 'sms_logs';
        }
        if (Schema::hasTable('smslogs')) {
            return 'smslogs';
        }
        if (Schema::hasTable('s_m_slogs')) {
            return 's_m_slogs';
        }

        return null;
    }
}
