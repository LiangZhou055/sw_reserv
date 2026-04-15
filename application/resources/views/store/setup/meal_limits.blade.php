@extends('store.layout')

@section('title', 'Reservation Slot Limits')

@section('content')
@php
  $weekdayOptions = [
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday',
    7 => 'Sunday',
  ];
@endphp
@if(isset($hasWeekdayTemplate) && !$hasWeekdayTemplate)
  <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
    Missing table <code>reservation_slot_weekday_limits</code>. Please run SQL update first.
  </div>
@endif
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="mt-4 flex flex-wrap gap-2">
    @foreach($weekdayOptions as $val => $label)
      <a href="{{ route('store.reservation_slot_limits.index', ['storeCode' => $storeCode, 'weekday' => $val]) }}"
         class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold transition {{ (int) $weekday === $val ? 'bg-blue-600 text-white' : 'border border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>
</div>

<form method="POST" action="{{ route('store.reservation_slot_limits.save', ['storeCode' => $storeCode]) }}">
  @csrf
  <input type="hidden" name="weekday" value="{{ $weekday }}">
  <div class="mt-4">
    <label class="inline-flex items-center gap-2 text-sm text-slate-700 mr-4">
      <input type="checkbox" name="apply_all_weekdays" value="1">
      Apply same limits to all weekdays
    </label>
    <button class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition">
      Save Limits
    </button>
  </div>

  <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
  @foreach($meals as $meal)
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
      <div class="border-b border-slate-200 px-5 py-4">
        <h4 class="text-base font-bold text-slate-900">
          {{ $meal->name }} ({{ substr($meal->start_time, 0, 5) }} - {{ substr($meal->end_time, 0, 5) }})
        </h4>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Slot</th>
              <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Max Reservations</th>
              <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Max Persons</th>
              <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">Enabled</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @foreach(($slots[$meal->id] ?? []) as $slotTime)
              @php $slotKey = substr($slotTime, 0, 5); @endphp
              @php
                $sourceVal = \App\Models\Event::SOURCE_CUSTOMER;
                $k = $meal->id.'|'.$slotKey.'|'.$sourceVal;
                $limit = $limits[$k] ?? null;
              @endphp
              <tr>
                <td class="px-3 py-2 text-sm font-semibold text-slate-800">{{ $slotKey }}</td>
                <td class="px-3 py-2">
                  <input type="number" min="0" name="limits[{{ $k }}][max_reservations]" value="{{ $limit->max_reservations ?? 10 }}"
                         class="w-24 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30">
                </td>
                <td class="px-3 py-2">
                  <input type="number" min="0" name="limits[{{ $k }}][max_persons]" value="{{ $limit->max_persons ?? 30 }}"
                         class="w-24 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30">
                </td>
                <td class="px-3 py-2">
                  <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="limits[{{ $k }}][is_enabled]" value="1" {{ isset($limit) ? ((int) $limit->is_enabled === 1 ? 'checked' : '') : 'checked' }}>
                    Enabled
                  </label>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endforeach
  </div>

</form>
@endsection
