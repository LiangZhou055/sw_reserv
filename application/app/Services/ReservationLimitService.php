<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Meal;
use App\Models\ReservationSlotWeekdayLimit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ReservationLimitService
{
    private const DEFAULT_MAX_RESERVATIONS = 10;
    private const DEFAULT_MAX_PERSONS = 30;

    public function normalizeSlotTime(string $time): ?string
    {
        $parts = explode(':', $time);
        if (count($parts) < 2) {
            return null;
        }
        $hour = (int) $parts[0];
        $minute = (int) $parts[1];
        if ($hour < 0 || $hour > 23) {
            return null;
        }
        if (!in_array($minute, [0, 30], true)) {
            return null;
        }
        return sprintf('%02d:%02d:00', $hour, $minute);
    }

    public function buildSlotsForMeal(Meal $meal): array
    {
        $slots = [];
        $start = strtotime((string) $meal->start_time);
        $end = strtotime((string) $meal->end_time);
        for ($t = $start; $t <= $end; $t += 1800) {
            $slots[] = date('H:i:s', $t);
        }
        return $slots;
    }

    public function checkAvailability(string $date, string $time, int $source, int $personNo, ?int $excludeEventId = null): array
    {
        $slotTime = $this->normalizeSlotTime($time);
        if ($slotTime === null) {
            return ['ok' => false, 'error' => 'Time must be on 30-minute boundary'];
        }

        $meal = Meal::query()
            ->where('is_active', 1)
            ->where('start_time', '<=', $slotTime)
            ->where('end_time', '>=', $slotTime)
            ->orderBy('sort_order')
            ->first();

        if (! $meal) {
            return ['ok' => false, 'error' => 'No active meal for this slot'];
        }

        $weekday = (int) date('N', strtotime($date));
        $limit = null;
        if (Schema::hasTable('reservation_slot_weekday_limits')) {
            $limit = ReservationSlotWeekdayLimit::query()
                ->where('weekday', $weekday)
                ->where('meal_id', $meal->id)
                ->where('slot_time', $slotTime)
                ->where('source', $source)
                ->first();
        }

        $maxReservations = (int) ($limit->max_reservations ?? self::DEFAULT_MAX_RESERVATIONS);
        $maxPersons = (int) ($limit->max_persons ?? self::DEFAULT_MAX_PERSONS);
        $isEnabled = (int) ($limit->is_enabled ?? 1);

        if ($isEnabled !== 1) {
            return ['ok' => false, 'error' => 'Slot is disabled'];
        }

        $used = Event::query()
            ->whereDate('start', $date)
            ->whereRaw('LEFT(`time`, 5) = ?', [substr($slotTime, 0, 5)])
            ->whereIn('status', [Event::STATUS_WAITING, Event::STATUS_DINE]);

        if ($excludeEventId !== null) {
            $used->where('id', '!=', $excludeEventId);
        }

        $usedCount = (clone $used)->count();
        $usedPersons = (int) (clone $used)->sum('person_no');

        if ($usedCount >= $maxReservations) {
            return ['ok' => false, 'error' => 'SLOT_FULL_COUNT'];
        }
        if (($usedPersons + $personNo) > $maxPersons) {
            return ['ok' => false, 'error' => 'SLOT_FULL_PERSONS'];
        }

        return [
            'ok' => true,
            'meal_id' => $meal->id,
            'meal_code' => $meal->code,
            'slot_time' => $slotTime,
        ];
    }

    public function getAvailability(string $date, int $source): Collection
    {
        $weekday = (int) date('N', strtotime($date));

        $meals = Meal::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get(['id', 'code', 'name', 'start_time', 'end_time', 'sort_order']);

        if ($meals->isEmpty()) {
            return collect();
        }

        $templates = collect();
        if (Schema::hasTable('reservation_slot_weekday_limits')) {
            $templates = ReservationSlotWeekdayLimit::query()
                ->where('weekday', $weekday)
                ->where('source', $source)
                ->whereIn('meal_id', $meals->pluck('id'))
                ->get()
                ->keyBy(function ($row) {
                    return $row->meal_id.'|'.substr((string) $row->slot_time, 0, 5).'|'.$row->source;
                });
        }

        $usage = Event::query()
            ->whereDate('start', $date)
            ->whereIn('status', [Event::STATUS_WAITING, Event::STATUS_DINE])
            ->selectRaw('LEFT(`time`, 5) as time_key, COUNT(*) as used_count, SUM(person_no) as used_persons')
            ->groupBy('time_key')
            ->get()
            ->keyBy('time_key');

        $rows = collect();
        foreach ($meals as $meal) {
            foreach ($this->buildSlotsForMeal($meal) as $slotTime) {
                $timeKey = substr((string) $slotTime, 0, 5);
                $key = $meal->id.'|'.$timeKey.'|'.$source;
                $template = $templates->get($key);

                $maxReservations = (int) ($template->max_reservations ?? self::DEFAULT_MAX_RESERVATIONS);
                $maxPersons = (int) ($template->max_persons ?? self::DEFAULT_MAX_PERSONS);
                $isEnabled = (int) ($template->is_enabled ?? 1);

                if ($isEnabled !== 1) {
                    continue;
                }

                $used = $usage->get($timeKey);
                $usedCount = (int) ($used->used_count ?? 0);
                $usedPersons = (int) ($used->used_persons ?? 0);

                $rows->push([
                    'meal_id' => (int) $meal->id,
                    'meal_code' => $meal->code,
                    'meal_name' => $meal->name,
                    'slot_time' => $timeKey,
                    'max_reservations' => $maxReservations,
                    'used_reservations' => $usedCount,
                    'remaining_reservations' => max($maxReservations - $usedCount, 0),
                    'max_persons' => $maxPersons,
                    'used_persons' => $usedPersons,
                    'remaining_persons' => max($maxPersons - $usedPersons, 0),
                ]);
            }
        }

        return $rows;
    }
}
