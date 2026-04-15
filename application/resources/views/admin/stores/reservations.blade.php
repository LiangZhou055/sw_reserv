@extends('admin.layout')

@section('title', 'Reservation Details')

@section('content')
@php
  $statusLabels = \App\Models\Event::statusLabels();
  $smsStatusLabels = \App\Models\Event::smsStatusLabels();
  $sourceLabels = \App\Models\Event::sourceLabels();
@endphp
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex items-center justify-between gap-4">
    <h3 class="text-base font-bold text-slate-900">Reservation Details - {{ strtoupper($store->code) }}</h3>
    <a class="inline-flex rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition"
       href="{{ route('admin.stores.show', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
      Back to store
    </a>
  </div>
  <form method="GET" action="{{ route('admin.stores.reservations.index', ['code' => $store->code]) }}" class="mt-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-8 lg:items-end">
      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start date</label>
        <input type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="start_date" value="{{ $startDate }}">
      </div>
      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End date</label>
        <input type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="end_date" value="{{ $endDate }}">
      </div>
      <div class="lg:col-span-1">
        <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Apply</button>
      </div>
  </form>
</div>

<div class="mt-4 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
    <h4 class="text-base font-bold text-slate-900">List</h4>
    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">{{ $events->total() }} rows</span>
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
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">SMS</th>
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
               href="{{ route('admin.stores.reservations.show', ['code' => $store->code, 'id' => $event->id, 'start_date' => $startDate, 'end_date' => $endDate, 'page' => $events->currentPage()]) }}">
              Detail
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="px-5 py-8 text-center text-sm text-slate-500">No reservation data.</td></tr>
      @endforelse
      </tbody>
    </table>
    @if($events->hasPages())
      <div class="flex items-center justify-between border-t border-slate-200 px-5 py-4">
        <div class="text-sm text-slate-500">
          Page {{ $events->currentPage() }} of {{ $events->lastPage() }}
        </div>
        <div class="inline-flex items-center gap-2">
          @if($events->onFirstPage())
            <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-400" disabled>Previous</button>
          @else
            <a class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition" href="{{ $events->previousPageUrl() }}">Previous</a>
          @endif

          @if($events->hasMorePages())
            <a class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition" href="{{ $events->nextPageUrl() }}">Next</a>
          @else
            <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-400" disabled>Next</button>
          @endif
        </div>
      </div>
    @endif
  </div>
</div>
@endsection

