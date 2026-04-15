@extends('store.layout')

@section('title', 'Reservation Detail')

@section('content')
@php
  $statusText = \App\Models\Event::statusLabel($event->status);
  $smsStatusText = \App\Models\Event::smsStatusLabel($event->sms_status);
  $sourceText = \App\Models\Event::sourceLabel($event->source ?? \App\Models\Event::SOURCE_STORE);
@endphp
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex items-center justify-between gap-4">
    <h3 class="text-base font-bold text-slate-900">Reservation Detail</h3>
    <a class="inline-flex rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition"
       href="{{ route('store.reservations.index', ['storeCode' => $storeCode, 'start_date' => $startDate, 'end_date' => $endDate, 'page' => $page]) }}">
      Back to list
    </a>
  </div>
  <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-sm">
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">ID</span><p class="font-semibold text-slate-800">{{ $event->id }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Order No</span><p class="font-semibold text-slate-800">{{ $event->order_no }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Status</span><p class="font-semibold text-slate-800">{{ $statusText }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">SMS Status</span><p class="font-semibold text-slate-800">{{ $smsStatusText }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Source</span><p class="font-semibold text-slate-800">{{ $sourceText }}</p></div>

    <div class="rounded-xl bg-slate-50 p-3 sm:col-span-2 lg:col-span-3"><span class="text-slate-500">Name</span><p class="font-semibold text-slate-800">{{ $event->title }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Persons</span><p class="font-semibold text-slate-800">{{ $event->person_no }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Contact</span><p class="font-semibold text-slate-800">{{ $event->contact_no }}</p></div>

    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Date</span><p class="font-semibold text-slate-800">{{ $event->start }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Time</span><p class="font-semibold text-slate-800">{{ $event->time }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Created</span><p class="font-semibold text-slate-800">{{ $event->created_at }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Updated</span><p class="font-semibold text-slate-800">{{ $event->updated_at }}</p></div>

    <div class="rounded-xl bg-slate-50 p-3 sm:col-span-2 lg:col-span-4">
      <span class="text-slate-500">Comments</span>
      <div class="mt-1 rounded-lg border border-slate-200 bg-white p-3 text-slate-700">
        {{ $event->comments ?: 'N/A' }}
      </div>
    </div>
  </div>
</div>

<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex items-center justify-between gap-4">
    <h4 class="text-base font-bold text-slate-900">SMS Logs For This Reservation</h4>
    @if(!empty($smsTableName))
      <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ $smsLogs->count() }} rows</span>
    @endif
  </div>
  <div class="mt-4 space-y-2">
    @if(empty($smsTableName))
      <div class="text-sm text-slate-500">No SMS log table found in this store database.</div>
    @else
      @forelse($smsLogs as $log)
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
          <div class="mb-1 flex items-center justify-between">
            <div class="text-xs text-slate-500">{{ $log->initiated_time ?? '' }}</div>
            @php
              $sendStatus = (int) ($log->status ?? 0);
              $sendLabel = $sendStatus === 1 ? 'Sent' : ($sendStatus === 2 ? 'Failed' : 'Unknown');
            @endphp
            @if($sendStatus === 1)
              <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{{ $sendLabel }}</span>
            @elseif($sendStatus === 2)
              <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">{{ $sendLabel }}</span>
            @else
              <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">{{ $sendLabel }}</span>
            @endif
          </div>
          <div class="text-sm text-slate-800">{{ $log->message ?? '' }}</div>
        </div>
      @empty
        <div class="text-sm text-slate-500">No SMS logs for this reservation.</div>
      @endforelse
    @endif
  </div>
</div>
@endsection
