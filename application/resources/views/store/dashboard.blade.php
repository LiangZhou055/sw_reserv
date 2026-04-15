@extends('store.layout')

@section('title', 'Dashboard')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-400">Total Reservations</p>
    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalReservations }}</p>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-400">Waiting</p>
    <p class="mt-2 text-3xl font-extrabold text-amber-600">{{ $waitingCount }}</p>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-400">Canceled</p>
    <p class="mt-2 text-3xl font-extrabold text-rose-600">{{ $canceledCount }}</p>
  </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
    <h3 class="text-base font-bold text-slate-900">Reservation Chart</h3>
  </div>
  <form method="GET" action="{{ route('store.dashboard', ['storeCode' => $storeCode]) }}" class="mt-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-8 lg:items-end px-5">
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
  <div class="mt-4 rounded-xl border border-slate-200 bg-white p-3">
    <canvas id="storeReservationChart" height="90"></canvas>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = @json($dailyLabels);
  const reservations = @json($dailyAllValues);
  const canceled = @json($dailyCanceledValues);
  const canvas = document.getElementById('storeReservationChart');
  if (canvas) {
    new Chart(canvas, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Reservations',
          data: reservations,
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37,99,235,0.12)',
          tension: 0.25,
          fill: true
        }, {
          label: 'Canceled',
          data: canceled,
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239,68,68,0.1)',
          tension: 0.25,
          fill: true
        }]
      },
      options: {
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true } }
      }
    });
  }
</script>
@endpush

