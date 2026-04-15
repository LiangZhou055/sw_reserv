@extends('admin.layout')

@section('title', 'Users')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <h3 class="text-base font-bold text-slate-900">Create User</h3>
  <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
      @csrf
      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Name</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="name" placeholder="Name" required>
      </div>
      <div class="lg:col-span-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="email" type="email" placeholder="Email" required>
      </div>
      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Password</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="password" type="text" placeholder="Password" required>
      </div>
      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Role</label>
        <select class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="role" required>
          <option value="manager">manager</option>
          <option value="staff">staff</option>
          <option value="super">super</option>
        </select>
      </div>
      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Store Code</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="store_code" placeholder="store_code">
      </div>
      <div class="lg:col-span-1">
        <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Create</button>
      </div>
  </form>
  </div>

<div class="mt-4 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
  <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
    <h4 class="text-base font-bold text-slate-900">Users</h4>
    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">{{ $users->total() }} total</span>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">ID</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Store</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Role</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Active</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Password Reset</th>
          <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Toggle</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 bg-white">
      @foreach($users as $row)
        <tr class="hover:bg-slate-50/80">
          <td class="px-5 py-3 text-sm text-slate-700">{{ $row->id }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $row->store_code }}</td>
          <td class="px-5 py-3 text-sm font-semibold text-slate-800">{{ $row->name }}</td>
          <td class="px-5 py-3 text-sm text-slate-700">{{ $row->email }}</td>
          <td class="px-5 py-3"><span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-700">{{ $row->role }}</span></td>
          <td class="px-5 py-3">
            @if($row->is_active)
              <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Active</span>
            @else
              <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">Inactive</span>
            @endif
          </td>
          <td class="px-5 py-3 min-w-[260px]">
            <form method="POST" action="{{ route('admin.users.password', ['id' => $row->id]) }}" class="flex items-center gap-2">
              @csrf
              <input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="password" placeholder="new password" required>
              <button class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600 transition">Update</button>
            </form>
          </td>
          <td class="px-5 py-3">
            <form method="POST" action="{{ route('admin.users.toggle', ['id' => $row->id]) }}">
              @csrf
              <button class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">Toggle</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
  </div>
</div>
@endsection

