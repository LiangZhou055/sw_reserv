@extends('admin.layout')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
  <div class="flex items-start justify-between gap-4">
    <div>
      <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Overview</p>
      <h3 class="mt-1 text-2xl font-extrabold text-slate-900">Welcome back, {{ $user->name ?? $user->email }}</h3>
      <p class="mt-2 text-sm text-slate-500">Use the menu to manage stores, users, and daily operations.</p>
    </div>
    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">Platform</span>
  </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-3">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-400">Stores</p>
    <h4 class="mt-2 text-lg font-bold text-slate-900">Manage all stores</h4>
    <p class="mt-2 text-sm text-slate-500">View store status, reservations, and SMS activity.</p>
    <a href="{{ route('admin.stores.index') }}" class="mt-4 inline-flex rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">Open Stores</a>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-400">Users</p>
    <h4 class="mt-2 text-lg font-bold text-slate-900">Manage access control</h4>
    <p class="mt-2 text-sm text-slate-500">Create users, reset passwords, and toggle active state.</p>
    <a href="{{ route('admin.users.index') }}" class="mt-4 inline-flex rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">Open Users</a>
  </div>
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-xs uppercase tracking-wide text-slate-400">Session</p>
    <h4 class="mt-2 text-lg font-bold text-slate-900">Secure admin portal</h4>
    <p class="mt-2 text-sm text-slate-500">Sign out after completing management tasks.</p>
    <form method="POST" action="{{ route('admin.logout') }}" class="mt-4">
      @csrf
      <button class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">Logout</button>
    </form>
  </div>
</div>
@endsection

