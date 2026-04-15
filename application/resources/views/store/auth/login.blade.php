<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Store Login - {{ $storeCode }}</title>
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
<body class="min-h-screen bg-slate-950 font-sans">
<div class="relative min-h-screen overflow-hidden">
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_18%,rgba(37,99,235,0.45),transparent_40%),radial-gradient(circle_at_82%_8%,rgba(14,165,233,0.25),transparent_35%),linear-gradient(145deg,#020617_0%,#0f172a_48%,#111827_100%)]"></div>
  <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
    <div class="grid w-full gap-6 lg:grid-cols-2">
      <div class="hidden rounded-3xl border border-white/10 bg-white/5 p-10 text-white backdrop-blur lg:block">
        <p class="text-xs uppercase tracking-[0.22em] text-blue-200">Store Portal</p>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight">{{ strtoupper($storeCode) }} Operations</h1>
        <p class="mt-4 max-w-md text-sm text-slate-200/90">Track reservations, open calendar quickly, and manage daily service flow for this store.</p>
        <div class="mt-8 rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm">
          Store Code: <span class="font-bold">{{ strtoupper($storeCode) }}</span>
        </div>
      </div>

      <div class="rounded-3xl border border-white/15 bg-white/95 p-6 shadow-2xl shadow-slate-900/40 sm:p-8">
        <p class="text-xs uppercase tracking-[0.22em] text-blue-600">Store Login</p>
        <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Welcome back</h2>
        <p class="mt-2 text-sm text-slate-500">Sign in to access {{ strtoupper($storeCode) }} dashboard.</p>

        @if ($errors->any())
          <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
            <ul class="list-disc pl-5 space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('store.login.submit', ['storeCode' => $storeCode]) }}" class="mt-6 space-y-4">
          @csrf
          <div>
            <label for="email" class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
            <input id="email" type="email" name="email" required autofocus value="{{ old('email') }}"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30">
          </div>
          <div>
            <label for="password" class="mb-1 block text-sm font-semibold text-slate-700">Password</label>
            <input id="password" type="password" name="password" required
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30">
          </div>
          <label class="flex items-center gap-2 text-sm text-slate-600">
            <input class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" type="checkbox" id="remember" name="remember">
            Remember me
          </label>
          <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
            Login
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>

