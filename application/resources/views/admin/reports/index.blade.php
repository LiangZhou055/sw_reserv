@extends('admin.layout')

@section('title', 'Reports')

@section('content')
@php
  $filterAction = route('admin.reports.index');
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex flex-wrap items-start justify-between gap-4">
    <div>
      <h3 class="text-base font-bold text-slate-900">Reports</h3>
      <p class="mt-1 text-sm text-slate-500">SMS usage and reservation statistics by store and date range.</p>
    </div>
  </div>
  <form method="GET" action="{{ $filterAction }}" class="mt-4 grid gap-3 lg:grid-cols-12 lg:items-end">
    <div class="lg:col-span-3">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Store</label>
      <select name="store" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
        <option value="all" {{ ($storeCode ?? '') === 'all' ? 'selected' : '' }}>All stores (summary)</option>
        @foreach($stores as $s)
          <option value="{{ strtolower($s->code) }}" {{ strtolower((string) ($storeCode ?? '')) === strtolower((string) $s->code) ? 'selected' : '' }}>
            {{ strtoupper($s->code) }} — {{ $s->rest_name }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start date</label>
      <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
    </div>
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End date</label>
      <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
    </div>
    <div class="lg:col-span-2">
      <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Apply</button>
    </div>
    @if(($mode ?? '') === 'single' && !empty($store))
      <div class="lg:col-span-3 flex flex-wrap gap-2">
        <a href="{{ route('admin.stores.reservations.index', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="inline-flex flex-1 justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition">
          Reservation list
        </a>
        <a href="{{ route('admin.stores.smslogs.index', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="inline-flex flex-1 justify-center rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
          SMS logs
        </a>
      </div>
    @endif
  </form>
</div>

@if(($mode ?? '') === 'all')
<div class="mt-4 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="border-b border-slate-200 px-5 py-4">
    <h4 class="text-base font-bold text-slate-900">All stores summary</h4>
    <p class="mt-1 text-xs text-slate-500">{{ $startDate }} — {{ $endDate }}</p>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Store</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Reservations</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Canceled</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Customer</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">SMS Total</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">SMS Sent</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">SMS Failed</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">SMS Inbound</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 bg-white">
      @forelse($rows as $r)
        <tr class="hover:bg-slate-50/80">
          <td class="px-5 py-3 text-sm font-semibold text-slate-800">{{ strtoupper($r['code']) }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $r['name'] }}</td>
          @if($r['error'])
            <td colspan="7" class="px-5 py-3 text-sm text-rose-600">Error: {{ $r['error'] }}</td>
            <td class="px-5 py-3"></td>
          @else
            <td class="px-5 py-3 text-sm text-right text-slate-800">{{ $r['reservations'] }}</td>
            <td class="px-5 py-3 text-sm text-right text-slate-800">{{ $r['canceled'] }}</td>
            <td class="px-5 py-3 text-sm text-right text-slate-800">{{ $r['customer_source'] }}</td>
            <td class="px-5 py-3 text-sm text-right text-slate-800">{{ $r['sms_total'] }}</td>
            <td class="px-5 py-3 text-sm text-right text-emerald-700">{{ $r['sms_sent'] }}</td>
            <td class="px-5 py-3 text-sm text-right text-rose-700">{{ $r['sms_failed'] }}</td>
            <td class="px-5 py-3 text-sm text-right text-slate-800">{{ $r['sms_inbound'] }}</td>
            <td class="px-5 py-3 text-sm">
              <a href="{{ route('admin.reports.index', ['store' => strtolower($r['code']), 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                 class="font-semibold text-indigo-600 hover:text-indigo-800">Detail</a>
            </td>
          @endif
        </tr>
      @empty
        <tr><td colspan="10" class="px-5 py-8 text-center text-sm text-slate-500">No stores configured.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@else
@php
  $r = $report;
  $sms = $r['sms'];
  $statusCounts = $r['status_counts'];
  $smsStatusCounts = $r['sms_status_counts'];
  $sourceCounts = $r['source_counts'];
@endphp

<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h4 class="text-base font-bold text-slate-900">{{ strtoupper($store->code) }} — {{ $store->rest_name }}</h4>
    <span class="text-xs text-slate-500">{{ $startDate }} — {{ $endDate }}</span>
  </div>
</div>

<div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
  <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-500">Reservations</p>
    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $r['reservations_total'] }}</p>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-500">Canceled</p>
    <p class="mt-2 text-3xl font-extrabold text-rose-600">{{ $statusCounts[\App\Models\Event::STATUS_CANCEL] ?? 0 }}</p>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-500">SMS total</p>
    <p class="mt-2 text-3xl font-extrabold text-indigo-600">{{ $sms['total'] }}</p>
    @if($sms['table'])
      <p class="mt-1 text-xs text-slate-400">{{ $sms['table'] }}</p>
    @else
      <p class="mt-1 text-xs text-amber-600">No SMS table</p>
    @endif
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-500">SMS sent / failed</p>
    <p class="mt-2 text-3xl font-extrabold text-slate-900">
      <span class="text-emerald-600">{{ $sms['sent'] }}</span>
      <span class="text-slate-300">/</span>
      <span class="text-rose-600">{{ $sms['failed'] }}</span>
    </p>
    <p class="mt-1 text-xs text-slate-500">Outbound {{ $sms['outbound'] }} · Inbound {{ $sms['inbound'] }}</p>
  </div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-2">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h4 class="text-sm font-bold text-slate-900">Reservations by day</h4>
    <div class="mt-4 rounded-xl border border-slate-200 p-3">
      <canvas id="reservationsChart" height="100"></canvas>
    </div>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h4 class="text-sm font-bold text-slate-900">SMS by day</h4>
    @if($sms['table'])
      <div class="mt-4 rounded-xl border border-slate-200 p-3">
        <canvas id="smsChart" height="100"></canvas>
      </div>
    @else
      <p class="mt-4 text-sm text-slate-500">SMS log table not found for this store database.</p>
    @endif
  </div>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-3">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h4 class="text-sm font-bold text-slate-900">Reservation status</h4>
    <dl class="mt-4 space-y-2 text-sm">
      @foreach(\App\Models\Event::statusLabels() as $key => $label)
        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
          <dt class="text-slate-600">{{ $label }}</dt>
          <dd class="font-bold text-slate-900">{{ $statusCounts[$key] ?? 0 }}</dd>
        </div>
      @endforeach
    </dl>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h4 class="text-sm font-bold text-slate-900">Reservation source</h4>
    <dl class="mt-4 space-y-2 text-sm">
      @foreach(\App\Models\Event::sourceLabels() as $key => $label)
        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
          <dt class="text-slate-600">{{ $label }}</dt>
          <dd class="font-bold text-slate-900">{{ $sourceCounts[$key] ?? 0 }}</dd>
        </div>
      @endforeach
    </dl>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h4 class="text-sm font-bold text-slate-900">SMS status (events)</h4>
    <dl class="mt-4 space-y-2 text-sm max-h-64 overflow-y-auto">
      @foreach(\App\Models\Event::smsStatusLabels() as $key => $label)
        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
          <dt class="text-slate-600">{{ $label }}</dt>
          <dd class="font-bold text-slate-900">{{ $smsStatusCounts[$key] ?? 0 }}</dd>
        </div>
      @endforeach
    </dl>
  </div>
</div>
@endif
@endsection

@if(($mode ?? '') === 'single')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const resLabels = @json($report['daily_labels']);
  const resValues = @json($report['daily_reservations']);
  const resCanceled = @json($report['daily_canceled']);
  const resCanvas = document.getElementById('reservationsChart');
  if (resCanvas) {
    new Chart(resCanvas, {
      type: 'line',
      data: {
        labels: resLabels,
        datasets: [{
          label: 'Reservations',
          data: resValues,
          borderColor: '#4f46e5',
          backgroundColor: 'rgba(79,70,229,0.12)',
          tension: 0.25,
          fill: true
        }, {
          label: 'Canceled',
          data: resCanceled,
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239,68,68,0.1)',
          tension: 0.25,
          fill: true
        }]
      },
      options: { plugins: { legend: { display: true } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
  }

  const smsLabels = @json($report['sms']['daily_labels']);
  const smsTotal = @json($report['sms']['daily_total']);
  const smsSent = @json($report['sms']['daily_sent']);
  const smsFailed = @json($report['sms']['daily_failed']);
  const smsCanvas = document.getElementById('smsChart');
  if (smsCanvas) {
    new Chart(smsCanvas, {
      type: 'bar',
      data: {
        labels: smsLabels,
        datasets: [{
          label: 'Total SMS',
          data: smsTotal,
          backgroundColor: 'rgba(79,70,229,0.55)'
        }, {
          label: 'Sent OK',
          data: smsSent,
          backgroundColor: 'rgba(16,185,129,0.65)'
        }, {
          label: 'Failed',
          data: smsFailed,
          backgroundColor: 'rgba(239,68,68,0.65)'
        }]
      },
      options: { plugins: { legend: { display: true } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
  }
</script>
@endpush
@endif
