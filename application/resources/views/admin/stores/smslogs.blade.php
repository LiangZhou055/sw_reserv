@extends('admin.layout')

@section('title', 'SMS Logs')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex items-center justify-between gap-4">
    <h3 class="text-base font-bold text-slate-900">SMS Logs - {{ strtoupper($store->code) }}</h3>
    <a class="inline-flex rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition"
       href="{{ route('admin.stores.show', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
      Back to store
    </a>
  </div>
  <form method="GET" action="{{ route('admin.stores.smslogs.index', ['code' => $store->code]) }}" class="mt-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-8 lg:items-end">
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
    <h4 class="text-base font-bold text-slate-900">List @if($tableName)<span class="ml-1 text-xs text-slate-500">({{ $tableName }})</span>@endif</h4>
    @if($tableName && method_exists($logs, 'total'))
      <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">{{ $logs->total() }} rows</span>
    @endif
  </div>
  <div class="overflow-x-auto">
    @if(!$tableName)
      <div class="px-5 py-8 text-sm text-slate-500">No SMS log table found in this store database.</div>
    @else
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">ID</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">To</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Message</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Initiated Time</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Event ID</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
        @forelse($logs as $log)
          <tr class="hover:bg-slate-50/80">
            <td class="px-5 py-3 text-sm text-slate-700">{{ $log->id ?? '' }}</td>
            <td class="px-5 py-3 text-sm text-slate-700">{{ $log->to ?? '' }}</td>
            <td class="px-5 py-3 text-sm text-slate-700">{{ $log->type ?? '' }}</td>
            <td class="px-5 py-3 text-sm text-slate-700">{{ $log->status ?? '' }}</td>
            <td class="px-5 py-3 text-sm text-slate-700">{{ $log->message ?? '' }}</td>
            <td class="px-5 py-3 text-sm text-slate-600">{{ $log->initiated_time ?? '' }}</td>
            <td class="px-5 py-3 text-sm text-slate-700">{{ $log->event_id ?? '' }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-slate-500">No SMS logs in selected date range.</td></tr>
        @endforelse
        </tbody>
      </table>

      @if($logs->hasPages())
        <div class="flex items-center justify-between border-t border-slate-200 px-5 py-4">
          <div class="text-sm text-slate-500">
            Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
          </div>
          <div class="inline-flex items-center gap-2">
            @if($logs->onFirstPage())
              <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-400" disabled>Previous</button>
            @else
              <a class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition" href="{{ $logs->previousPageUrl() }}">Previous</a>
            @endif

            @if($logs->hasMorePages())
              <a class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition" href="{{ $logs->nextPageUrl() }}">Next</a>
            @else
              <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-400" disabled>Next</button>
            @endif
          </div>
        </div>
      @endif
    @endif
  </div>
</div>
@endsection

