@extends('store.layout')

@section('title', 'Meal Setup')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <h3 class="text-base font-bold text-slate-900">Meal Management</h3>
  <form method="POST" action="{{ route('store.meals.store', ['storeCode' => $storeCode]) }}" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
    @csrf
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Code</label>
      <input type="text" name="code" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm" placeholder="lunch" required>
    </div>
    <div class="lg:col-span-3">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Name</label>
      <input type="text" name="name" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm" placeholder="Lunch" required>
    </div>
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start</label>
      <input type="time" name="start_time" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm" required>
    </div>
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End</label>
      <input type="time" name="end_time" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm" required>
    </div>
    <div class="lg:col-span-1">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Order</label>
      <input type="number" name="sort_order" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm" value="0">
    </div>
    <div class="lg:col-span-1">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Active</label>
      <select name="is_active" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
    </div>
    <div class="lg:col-span-1">
      <button class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition">Add</button>
    </div>
  </form>

  <div class="mt-4 overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Code</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Start</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">End</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Order</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Active</th>
          <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 bg-white">
      @foreach($meals as $meal)
        <tr>
          <form method="POST" action="{{ route('store.meals.update', ['storeCode' => $storeCode, 'id' => $meal->id]) }}">
            @csrf
            <td class="px-4 py-3 text-sm font-semibold text-slate-700">{{ $meal->code }}</td>
            <td class="px-4 py-3"><input type="text" name="name" value="{{ $meal->name }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></td>
            <td class="px-4 py-3"><input type="time" name="start_time" value="{{ substr($meal->start_time, 0, 5) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></td>
            <td class="px-4 py-3"><input type="time" name="end_time" value="{{ substr($meal->end_time, 0, 5) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></td>
            <td class="px-4 py-3"><input type="number" name="sort_order" value="{{ $meal->sort_order }}" class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm"></td>
            <td class="px-4 py-3">
              <select name="is_active" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="1" {{ (int) $meal->is_active === 1 ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (int) $meal->is_active === 0 ? 'selected' : '' }}>No</option>
              </select>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <button class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">Save</button>
          </form>
              <form method="POST" action="{{ route('store.meals.delete', ['storeCode' => $storeCode, 'id' => $meal->id]) }}" class="inline">
                @csrf
                <button class="rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">Delete</button>
              </form>
            </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
