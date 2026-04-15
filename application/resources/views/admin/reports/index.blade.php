@extends('admin.layout')

@section('title', 'Reports')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="border-b border-slate-200 px-5 py-4">
    <h3 class="text-base font-bold text-slate-900">Store Daily Report</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Store</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Total Events</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Today Events</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Today Confirmed</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Today Canceled</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 bg-white">
      @forelse($rows as $r)
        <tr class="hover:bg-slate-50/80">
          <td class="px-5 py-3 text-sm font-semibold text-slate-800">{{ $r['code'] }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $r['name'] }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $r['total_events'] ?? 'N/A' }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $r['today_events'] ?? 'N/A' }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $r['today_confirmed'] ?? 'N/A' }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $r['today_canceled'] ?? 'N/A' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">No report rows.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

