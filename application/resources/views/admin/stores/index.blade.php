@extends('admin.layout')

@section('title', 'Stores')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
    <h3 class="text-base font-bold text-slate-900">Stores</h3>
    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">{{ count($stores) }} total</span>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Code</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Database</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 bg-white">
      @forelse($stores as $store)
        <tr class="hover:bg-slate-50/80">
          <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-700">{{ strtoupper($store->code) }}</span></td>
          <td class="px-5 py-3 text-sm font-semibold text-slate-800">{{ $store->rest_name }}</td>
          <td class="px-5 py-3 text-sm text-slate-600">{{ $store->db_database }}</td>
          <td class="px-5 py-3">
            @if($store->is_active)
              <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Active</span>
            @else
              <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">Inactive</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right">
            <a class="inline-flex rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition"
               href="{{ route('admin.stores.show', ['code' => $store->code]) }}">
              Open
            </a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">No stores found.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

