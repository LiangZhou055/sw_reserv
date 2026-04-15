@extends('store.layout')

@section('title', 'Reservations')

@section('content')
@php
  $statusLabels = \App\Models\Event::statusLabels();
  $smsStatusLabels = \App\Models\Event::smsStatusLabels();
  $sourceLabels = \App\Models\Event::sourceLabels();
@endphp
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <h3 class="text-base font-bold text-slate-900">Reservation Filters</h3>
  <form method="GET" action="{{ route('store.reservations.index', ['storeCode' => $storeCode]) }}" class="mt-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-8 lg:items-end">
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start date</label>
      <input type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30" name="start_date" value="{{ $startDate }}">
    </div>
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End date</label>
      <input type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30" name="end_date" value="{{ $endDate }}">
    </div>
    <div class="lg:col-span-1">
      <button class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition">Apply</button>
    </div>
  </form>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
    <h3 class="text-base font-bold text-slate-900">Reservations</h3>
    <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ $events->total() }} rows</span>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Order No</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Time</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Persons</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">SMS Status</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Source</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 bg-white">
      @forelse($events as $event)
        <tr class="hover:bg-slate-50/80">
          <td class="px-5 py-3 text-sm font-semibold text-slate-800">{{ $event->order_no }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $event->title }}</td>
          <td class="px-5 py-3 text-sm text-slate-600">{{ $event->start }}</td>
          <td class="px-5 py-3 text-sm text-slate-600">{{ $event->time }}</td>
          <td class="px-5 py-3 text-sm text-slate-600">{{ $event->person_no }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $statusLabels[(int) $event->status] ?? $event->status }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $smsStatusLabels[(int) $event->sms_status] ?? $event->sms_status }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $sourceLabels[(int) ($event->source ?? \App\Models\Event::SOURCE_STORE)] ?? ($event->source ?? \App\Models\Event::SOURCE_STORE) }}</td>
          <td class="px-5 py-3 text-right">
            <a class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition"
               href="{{ route('store.reservations.show', ['storeCode' => $storeCode, 'id' => $event->id, 'start_date' => $startDate, 'end_date' => $endDate, 'page' => $events->currentPage()]) }}">
              Detail
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="px-5 py-8 text-center text-sm text-slate-500">No reservation data.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="border-t border-slate-200 px-5 py-4">
    {{ $events->links() }}
  </div>
</div>
@endsection
