<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\ReservationLimitService;
use App\Services\StoreContext;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerReservationApiController extends Controller
{
    private ReservationLimitService $limitService;

    public function __construct(ReservationLimitService $limitService)
    {
        $this->limitService = $limitService;
    }

    public function store(Request $request, string $storeCode)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'person_no' => ['required', 'integer', 'min:1', 'max:1000'],
            'contact_no' => ['nullable', 'string', 'max:50'],
            'comments' => ['nullable', 'string', 'max:1000'],
        ]);

        $check = $this->limitService->checkAvailability(
            $data['date'],
            $data['time'],
            Event::SOURCE_CUSTOMER,
            (int) $data['person_no']
        );
        if (! $check['ok']) {
            return response()->json([
                'ok' => false,
                'error' => $check['error'],
            ], 422);
        }

        $payload = [
            'title' => $data['title'],
            'start' => $data['date'],
            'end' => $data['date'],
            'time' => $data['time'],
            'person_no' => $data['person_no'],
            'contact_no' => $data['contact_no'] ?? '',
            'comments' => $data['comments'] ?? '',
            'order_no' => $this->generateUniqueOrderNo(),
            'status' => Event::STATUS_WAITING,
            'sms_status' => Event::SMS_STATUS_IDLE,
            'source' => Event::SOURCE_CUSTOMER,
        ];

        $event = Event::create($payload);

        return response()->json([
            'ok' => true,
            'store_code' => $storeCode,
            'reservation' => [
                'id' => $event->id,
                'order_no' => $this->withStorePrefix((string) $event->order_no),
                'order_no_raw' => (string) $event->order_no,
                'date' => (string) $event->start,
                'time' => substr((string) $event->time, 0, 5),
                'person_no' => (int) $event->person_no,
                'contact_no' => (string) $event->contact_no,
                'status' => $event->status,
                'sms_status' => $event->sms_status,
                'source' => $event->source ?? Event::SOURCE_CUSTOMER,
            ],
        ], 201);
    }

    public function index(Request $request, string $storeCode)
    {
        $data = $request->validate([
            'contact_no' => ['required', 'string', 'max:50'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'unfinished_only' => ['nullable', 'boolean'],
            'source' => ['nullable', 'in:all,customer,store'],
        ]);

        $contactCandidates = $this->buildContactCandidates((string) $data['contact_no']);
        $unfinishedOnly = array_key_exists('unfinished_only', $data) ? (bool) $data['unfinished_only'] : true;
        $sourceFilter = (string) ($data['source'] ?? 'all');

        $query = Event::query()
            ->whereIn('contact_no', $contactCandidates)
            ->orderByRaw('CASE WHEN status = ? THEN 1 ELSE 0 END ASC', [Event::STATUS_CANCEL])
            ->orderBy('start')
            ->orderBy('time');

        if ($sourceFilter === 'customer') {
            $query->where('source', Event::SOURCE_CUSTOMER);
        } elseif ($sourceFilter === 'store') {
            $query->where('source', Event::SOURCE_STORE);
        }

        if ($unfinishedOnly) {
            $query->whereIn('status', [
                Event::STATUS_WAITING,
                Event::STATUS_DINE,
            ]);
        } else {
            $query->whereIn('status', [
                Event::STATUS_WAITING,
                Event::STATUS_DINE,
                Event::STATUS_CANCEL,
            ]);
        }

        if (!empty($data['date_from'])) {
            $query->whereDate('start', '>=', $data['date_from']);
        }
        if (!empty($data['date_to'])) {
            $query->whereDate('start', '<=', $data['date_to']);
        }

        $rows = $query->get([
            'id',
            'order_no',
            'title',
            'start',
            'time',
            'person_no',
            'contact_no',
            'status',
            'sms_status',
            'source',
            'comments',
            'created_at',
        ]);

        $today = Carbon::today()->toDateString();

        return response()->json([
            'ok' => true,
            'store_code' => $storeCode,
            'reservations' => $rows->map(function (Event $event) use ($today) {
                $date = (string) $event->start;
                $source = (int) ($event->source ?? Event::SOURCE_STORE);
                $canCancel = ((int) $event->status === Event::STATUS_WAITING)
                    && ($date >= $today)
                    && ($source === Event::SOURCE_CUSTOMER);
                return [
                    'id' => (int) $event->id,
                    'order_no' => $this->withStorePrefix((string) $event->order_no),
                    'order_no_raw' => (string) $event->order_no,
                    'title' => (string) $event->title,
                    'date' => $date,
                    'time' => substr((string) $event->time, 0, 5),
                    'person_no' => (int) $event->person_no,
                    'contact_no' => (string) $event->contact_no,
                    'status' => (int) $event->status,
                    'status_label' => Event::statusLabel($event->status),
                    'sms_status' => (int) $event->sms_status,
                    'sms_status_label' => Event::smsStatusLabel($event->sms_status),
                    'source' => $source,
                    'source_label' => Event::sourceLabel($source),
                    'can_cancel' => $canCancel,
                    'comments' => (string) ($event->comments ?? ''),
                    'created_at' => optional($event->created_at)->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function cancel(Request $request, string $storeCode, int $id)
    {
        $data = $request->validate([
            'contact_no' => ['required', 'string', 'max:50'],
            'reason' => ['nullable', 'string', 'max:300'],
        ]);

        $contactCandidates = $this->buildContactCandidates((string) $data['contact_no']);

        $event = Event::query()
            ->where('id', $id)
            ->where('source', Event::SOURCE_CUSTOMER)
            ->whereIn('contact_no', $contactCandidates)
            ->first();

        if (! $event) {
            return response()->json([
                'ok' => false,
                'error' => 'Reservation not found',
            ], 404);
        }

        if ((int) $event->status === Event::STATUS_CANCEL) {
            return response()->json([
                'ok' => true,
                'reservation' => [
                    'id' => (int) $event->id,
                    'status' => (int) $event->status,
                    'status_label' => Event::statusLabel($event->status),
                ],
            ]);
        }

        if ((int) $event->status !== Event::STATUS_WAITING) {
            return response()->json([
                'ok' => false,
                'error' => 'Reservation cannot be canceled at current status',
            ], 422);
        }

        $event->status = Event::STATUS_CANCEL;
        $event->sms_status = Event::SMS_STATUS_CANCELED;
        if (!empty($data['reason'])) {
            $prefix = trim((string) $event->comments);
            $event->comments = trim($prefix.' Canceled '.$data['reason']);
        }
        $event->save();

        return response()->json([
            'ok' => true,
            'reservation' => [
                'id' => (int) $event->id,
                'status' => (int) $event->status,
                'status_label' => Event::statusLabel($event->status),
            ],
        ]);
    }

    public function availability(Request $request, string $storeCode)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'source' => ['nullable', 'in:1,2'],
        ]);

        $source = (int) ($data['source'] ?? Event::SOURCE_CUSTOMER);
        $queryDate = Carbon::parse((string) $data['date'])->toDateString();
        $rows = $this->limitService->getAvailability($queryDate, $source);

        // If querying today, only return slots at/after current time.
        if ($queryDate === Carbon::today()->toDateString()) {
            $currentTime = Carbon::now()->format('H:i');
            $rows = $rows->filter(static function ($slot) use ($currentTime) {
                $slotTime = (string) ($slot['slot_time'] ?? '');
                return $slotTime !== '' && strcmp($slotTime, $currentTime) >= 0;
            })->values();
        }

        return response()->json([
            'ok' => true,
            'store_code' => $storeCode,
            'date' => $queryDate,
            'source' => $source,
            'slots' => $rows->values(),
        ]);
    }

    private function generateUniqueOrderNo(): int
    {
        $min = 10000;
        $max = 99999;
        $retry = 30;

        for ($i = 0; $i < $retry; $i++) {
            $candidate = random_int($min, $max);
            if (! Event::where('order_no', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Failed to generate unique 5-digit order number');
    }

    private function buildContactCandidates(string $raw): array
    {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return [''];
        }

        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';
        $candidates = [
            $trimmed,
            str_replace(' ', '', $trimmed),
            str_replace('-', '', $trimmed),
            ltrim($trimmed, '+'),
        ];

        if ($digits !== '') {
            $candidates[] = $digits;
            $candidates[] = '+'.$digits;
        }

        return array_values(array_unique(array_filter($candidates, static fn ($v) => $v !== '')));
    }

    private function withStorePrefix(string $orderNo): string
    {
        $prefix = trim((string) StoreContext::getRestPrefix());
        return $prefix.$orderNo;
    }
}
