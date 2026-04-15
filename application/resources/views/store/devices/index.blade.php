@extends('store.layout')

@section('title', 'Devices')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
    <h3 class="text-base font-bold text-slate-900">Devices</h3>
    <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ $devices->total() }} total</span>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">ID</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">SN</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
          <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 bg-white">
      @forelse($devices as $device)
        <tr class="hover:bg-slate-50/80">
          <td class="px-5 py-3 text-sm text-slate-700">{{ $device->id }}</td>
          <td class="px-5 py-3 text-sm font-semibold text-slate-800">{{ $device->sn }}</td>
          <td class="px-5 py-3">
            @if((int) $device->status === 1)
              <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Active</span>
            @else
              <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">Inactive</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right">
            @if((int) $device->status === 1)
              <form method="POST" action="{{ route('store.devices.status', ['storeCode' => $storeCode, 'id' => $device->id]) }}" class="inline">
                @csrf
                <input type="hidden" name="status" value="0">
                <button class="inline-flex rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                  Deactivate
                </button>
              </form>
            @else
              <form method="POST" action="{{ route('store.devices.status', ['storeCode' => $storeCode, 'id' => $device->id]) }}" class="inline">
                @csrf
                <input type="hidden" name="status" value="1">
                <button class="inline-flex rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                  Activate
                </button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">No device records.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="border-t border-slate-200 px-5 py-4">
    {{ $devices->links() }}
  </div>
</div>
@endsection
