@extends('admin.layout')

@section('title', 'Store Detail')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <div class="flex items-center justify-between">
    <h3 class="text-base font-bold text-slate-900">Store: {{ strtoupper($store->code) }}</h3>
    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">{{ $store->rest_name }}</span>
  </div>
  <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-sm">
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Name</span><p class="font-semibold text-slate-800">{{ $store->rest_name }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Prefix</span><p class="font-semibold text-slate-800">{{ $store->rest_prefix }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Database</span><p class="font-semibold text-slate-800">{{ $store->db_database }}</p></div>
    <div class="rounded-xl bg-slate-50 p-3"><span class="text-slate-500">Twilio From</span><p class="font-semibold text-slate-800">{{ $store->twilio_from }}</p></div>
  </div>
</div>

<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  @php
    $apiKeyHash = data_get($store, 'api_key_hash');
    $apiKeyEnabled = (int) data_get($store, 'api_key_enabled', 0);
    $apiKeyRotatedAt = data_get($store, 'api_key_rotated_at');
    $hasApiKeyHash = (bool) ($hasApiKeyHash ?? false);
    $hasApiKeyEnabled = (bool) ($hasApiKeyEnabled ?? false);
    $hasApiKeyRotatedAt = (bool) ($hasApiKeyRotatedAt ?? false);
  @endphp
  <h4 class="text-sm font-bold text-slate-900">API Key Settings</h4>
  @if(!$hasApiKeyHash || !$hasApiKeyEnabled || !$hasApiKeyRotatedAt)
    <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
      Central `stores` table is missing some API key columns. Feature still works with available columns, but please run latest SQL/migration.
    </div>
  @endif
  <div class="mt-3 grid gap-3 sm:grid-cols-3 text-sm">
    <div class="rounded-xl bg-slate-50 p-3">
      <span class="text-slate-500">Configured</span>
      <p class="font-semibold text-slate-800">{{ empty($apiKeyHash) ? 'No' : 'Yes' }}</p>
    </div>
    <div class="rounded-xl bg-slate-50 p-3">
      <span class="text-slate-500">Enabled</span>
      <p class="font-semibold text-slate-800">{{ $hasApiKeyEnabled ? ($apiKeyEnabled === 1 ? 'Yes' : 'No') : '-' }}</p>
    </div>
    <div class="rounded-xl bg-slate-50 p-3">
      <span class="text-slate-500">Rotated At</span>
      <p class="font-semibold text-slate-800">{{ $hasApiKeyRotatedAt ? ($apiKeyRotatedAt ?? '-') : '-' }}</p>
    </div>
  </div>
  <form method="POST" action="{{ route('admin.stores.api_key.update', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="mt-4 grid gap-3 lg:grid-cols-12 lg:items-end">
    @csrf
    <div class="lg:col-span-6">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">API Key (leave blank to keep current)</label>
      <input type="text" name="api_key" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" placeholder="Set new API key">
    </div>
    <div class="lg:col-span-2">
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Enabled</label>
      <select name="api_key_enabled" {{ $hasApiKeyEnabled ? '' : 'disabled' }} class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 {{ $hasApiKeyEnabled ? '' : 'bg-slate-100 text-slate-400' }}">
        <option value="1" {{ $apiKeyEnabled === 1 ? 'selected' : '' }}>Enabled</option>
        <option value="0" {{ $apiKeyEnabled === 0 ? 'selected' : '' }}>Disabled</option>
      </select>
    </div>
    <div class="lg:col-span-2">
      <button type="submit"
              data-swift-confirm="1"
              data-confirm-title="Save API Key"
              data-confirm-text="Confirm save API key settings for this store?"
              class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">
        Save API Key
      </button>
    </div>
    <div class="lg:col-span-2">
      <button type="submit" name="action" value="generate"
              data-swift-confirm="1"
              data-confirm-title="Generate Random API Key"
              data-confirm-text="Confirm generate a new random API key? This will replace the current key."
              class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
        Generate Random
      </button>
    </div>
  </form>
</div>

<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  @php
    $hasSmsTplWelcome = (bool) ($hasSmsTplWelcome ?? false);
    $hasSmsTplNotice = (bool) ($hasSmsTplNotice ?? false);
    $hasSmsTplConfirm = (bool) ($hasSmsTplConfirm ?? false);
    $hasSmsTplCancel = (bool) ($hasSmsTplCancel ?? false);
  @endphp
  <h4 class="text-sm font-bold text-slate-900">SMS Templates</h4>
  <p class="mt-1 text-xs text-slate-500">
    Variables: <code>{store_name}</code> <code>{order_no}</code> <code>{date}</code> <code>{time}</code> <code>{party_size}</code> <code>{confirm_keyword}</code> <code>{cancel_keyword}</code>
  </p>
  @if(!$hasSmsTplWelcome || !$hasSmsTplNotice || !$hasSmsTplConfirm || !$hasSmsTplCancel)
    <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
      Central `stores` table is missing one or more SMS template columns.
    </div>
  @endif
  <form method="POST" action="{{ route('admin.stores.sms_templates.update', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="mt-4 grid gap-3">
    @csrf
    <div>
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Welcome Template</label>
      <textarea name="sms_tpl_welcome" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ data_get($store, 'sms_tpl_welcome') }}</textarea>
    </div>
    <div>
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Notice Template</label>
      <textarea name="sms_tpl_notice" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ data_get($store, 'sms_tpl_notice') }}</textarea>
    </div>
    <div>
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Confirm Template</label>
      <textarea name="sms_tpl_confirm" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ data_get($store, 'sms_tpl_confirm') }}</textarea>
    </div>
    <div>
      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cancel Template</label>
      <textarea name="sms_tpl_cancel" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ data_get($store, 'sms_tpl_cancel') }}</textarea>
    </div>
    <div>
      <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Save SMS Templates</button>
    </div>
  </form>
</div>

<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <h4 class="text-sm font-bold text-slate-900">Filter</h4>
  <form method="GET" action="{{ route('admin.stores.show', ['code' => $store->code]) }}" class="mt-3 grid gap-3 lg:grid-cols-12 lg:items-end">
      <div class="lg:col-span-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start date</label>
        <input type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="start_date" value="{{ $startDate }}">
      </div>
      <div class="lg:col-span-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End date</label>
        <input type="date" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30" name="end_date" value="{{ $endDate }}">
      </div>
      <div class="lg:col-span-2">
        <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition">Apply</button>
      </div>
      <div class="lg:col-span-2">
        <a class="inline-flex w-full justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition"
           href="{{ route('admin.stores.reservations.index', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
          Reservation Details
        </a>
      </div>
      <div class="lg:col-span-2">
        <a class="inline-flex w-full justify-center rounded-lg border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition"
           href="{{ route('admin.stores.smslogs.index', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
          SMS Logs
        </a>
      </div>
  </form>
</div>

<div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
  <h4 class="text-sm font-bold text-rose-700">Danger Zone</h4>
  <p class="mt-2 text-sm text-rose-700">
    Purge old data for this store. The system keeps current month + previous month, and deletes anything before
    <span class="font-semibold">{{ $purgeCutoffDate }}</span>.
  </p>
  <form method="POST" action="{{ route('admin.stores.purge', ['code' => $store->code, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="mt-3">
    @csrf
    <button type="submit"
            data-swift-confirm="1"
            data-confirm-title="Purge Old Data"
            data-confirm-text="This will permanently delete old data for store {{ strtoupper($store->code) }}. Continue?"
            data-confirm-ok="Purge"
            class="rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 transition">
      Purge Old Data
    </button>
  </form>
</div>

<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
  <h4 class="text-base font-bold text-slate-900">Charts</h4>
  <div class="mt-4 grid gap-3 md:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
      <div class="text-xs uppercase tracking-wide text-slate-500">Waiting</div>
      <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $statusCounts[\App\Models\Event::STATUS_WAITING] ?? 0 }}</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
      <div class="text-xs uppercase tracking-wide text-slate-500">Dine In</div>
      <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $statusCounts[\App\Models\Event::STATUS_DINE] ?? 0 }}</div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
      <div class="text-xs uppercase tracking-wide text-slate-500">Canceled</div>
      <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $statusCounts[\App\Models\Event::STATUS_CANCEL] ?? 0 }}</div>
    </div>
  </div>
  <div class="mt-6 rounded-xl border border-slate-200 bg-white p-3">
    <canvas id="dailyEventsChart" height="90"></canvas>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = @json($dailyLabels);
  const values = @json($dailyValues);
  const canceledValues = @json($dailyCanceledValues ?? []);
  const canvas = document.getElementById('dailyEventsChart');
  if (canvas) {
    new Chart(canvas, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Reservations',
          data: values,
          borderColor: '#4f46e5',
          backgroundColor: 'rgba(79,70,229,0.15)',
          tension: 0.25,
          fill: true
        },
        {
          label: 'Canceled',
          data: canceledValues,
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239,68,68,0.12)',
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

