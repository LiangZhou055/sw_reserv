<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif']
            }
          }
        }
      };
    </script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 font-sans">
<div class="flex min-h-screen">
  <aside class="hidden lg:flex lg:w-64 lg:flex-col bg-slate-950 text-slate-200">
    <div class="px-6 py-6 border-b border-slate-800">
      <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Reserve SaaS</p>
      <h1 class="mt-1 text-xl font-extrabold text-white">Super Admin</h1>
    </div>
    <nav class="px-4 py-6 space-y-1">
      <a href="{{ route('admin.dashboard') }}"
         class="block rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        Dashboard
      </a>
      <a href="{{ route('admin.stores.index') }}"
         class="block rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.stores.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        Stores
      </a>
      <a href="{{ route('admin.users.index') }}"
         class="block rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        Users
      </a>
    </nav>
  </aside>

  <main class="flex-1 min-w-0">
    <header class="bg-white/90 backdrop-blur border-b border-slate-200">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">
        <div>
          <p class="text-xs text-slate-500 uppercase tracking-[0.18em]">Admin Panel</p>
          <h2 class="text-lg font-bold text-slate-900">@yield('title', 'Admin')</h2>
        </div>
        <div class="flex items-center gap-3">
          @if(!empty($user))
            <div class="hidden sm:block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
              <p class="text-xs text-slate-500">Signed in as</p>
              <p class="text-sm font-semibold text-slate-800">{{ $user->name ?? $user->email }}</p>
            </div>
          @endif
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition">
              Logout
            </button>
          </form>
        </div>
      </div>
    </header>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 space-y-4">
      @if(session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
          {{ session('status') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @yield('content')
    </section>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('click', function (event) {
    const trigger = event.target.closest('[data-swift-confirm]');
    if (!trigger) return;

    event.preventDefault();
    const form = trigger.closest('form');
    const title = trigger.getAttribute('data-confirm-title') || 'Please Confirm';
    const text = trigger.getAttribute('data-confirm-text') || 'Are you sure?';
    const confirmText = trigger.getAttribute('data-confirm-ok') || 'Confirm';
    const cancelText = trigger.getAttribute('data-confirm-cancel') || 'Cancel';

    Swal.fire({
      icon: 'warning',
      title: title,
      text: text,
      showCancelButton: true,
      confirmButtonText: confirmText,
      cancelButtonText: cancelText,
      reverseButtons: true
    }).then(function (result) {
      if (result.isConfirmed && form) {
        if (typeof form.requestSubmit === 'function') {
          form.requestSubmit(trigger);
          return;
        }
        if (trigger.name) {
          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = trigger.name;
          hidden.value = trigger.value || '';
          form.appendChild(hidden);
        }
        form.submit();
      }
    });
  });
</script>
@stack('scripts')
</body>
</html>

