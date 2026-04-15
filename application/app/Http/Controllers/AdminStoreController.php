<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AdminStoreController extends Controller
{
    public function index()
    {
        $stores = DB::connection('central')
            ->table('stores')
            ->orderBy('code')
            ->get();

        return view('admin.stores.index', [
            'stores' => $stores,
            'user' => auth('store')->user(),
        ]);
    }

    public function show(Request $request, string $code)
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if (! $store) {
            abort(404, 'Store not found');
        }

        StoreContext::applyForStore($store->code);

        $hasApiKeyHash = Schema::connection('central')->hasColumn('stores', 'api_key_hash');
        $hasApiKeyEnabled = Schema::connection('central')->hasColumn('stores', 'api_key_enabled');
        $hasApiKeyRotatedAt = Schema::connection('central')->hasColumn('stores', 'api_key_rotated_at');
        $hasApiKeyLast4 = Schema::connection('central')->hasColumn('stores', 'api_key_last4');
        $hasSmsTplWelcome = Schema::connection('central')->hasColumn('stores', 'sms_tpl_welcome');
        $hasSmsTplNotice = Schema::connection('central')->hasColumn('stores', 'sms_tpl_notice');
        $hasSmsTplConfirm = Schema::connection('central')->hasColumn('stores', 'sms_tpl_confirm');
        $hasSmsTplCancel = Schema::connection('central')->hasColumn('stores', 'sms_tpl_cancel');

        $startDate = $request->query('start_date', now()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $base = Event::query()
            ->whereDate('start', '>=', $startDate)
            ->whereDate('start', '<=', $endDate);

        $statusCounts = (clone $base)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $dailyCounts = (clone $base)
            ->selectRaw('DATE(start) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailyCanceledCounts = (clone $base)
            ->where('status', Event::STATUS_CANCEL)
            ->selectRaw('DATE(start) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailyLabels = $dailyCounts->pluck('day')->toArray();
        $dailyValues = $dailyCounts->pluck('cnt', 'day')->toArray();
        $dailyCanceledValues = $dailyCanceledCounts->pluck('cnt', 'day')->toArray();

        $dailySeries = [];
        $dailyCanceledSeries = [];
        foreach ($dailyLabels as $label) {
            $dailySeries[] = (int) ($dailyValues[$label] ?? 0);
            $dailyCanceledSeries[] = (int) ($dailyCanceledValues[$label] ?? 0);
        }

        return view('admin.stores.show', [
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'purgeCutoffDate' => now()->startOfMonth()->subMonth()->toDateString(),
            'hasApiKeyHash' => $hasApiKeyHash,
            'hasApiKeyEnabled' => $hasApiKeyEnabled,
            'hasApiKeyRotatedAt' => $hasApiKeyRotatedAt,
            'hasSmsTplWelcome' => $hasSmsTplWelcome,
            'hasSmsTplNotice' => $hasSmsTplNotice,
            'hasSmsTplConfirm' => $hasSmsTplConfirm,
            'hasSmsTplCancel' => $hasSmsTplCancel,
            'statusCounts' => $statusCounts,
            'dailyLabels' => $dailyLabels,
            'dailyValues' => $dailySeries,
            'dailyCanceledValues' => $dailyCanceledSeries,
            'user' => auth('store')->user(),
        ]);
    }

    public function purge(Request $request, string $code)
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if (! $store) {
            abort(404, 'Store not found');
        }

        StoreContext::applyForStore($store->code);

        // Keep current month + previous month; delete anything older.
        $cutoffDate = now()->startOfMonth()->subMonth()->toDateString();

        $deletedEvents = Event::query()
            ->whereDate('start', '<', $cutoffDate)
            ->delete();

        $deletedMemos = 0;
        if (Schema::hasTable('memos') && Schema::hasColumn('memos', 'memo_date')) {
            $deletedMemos = DB::table('memos')
                ->whereDate('memo_date', '<', $cutoffDate)
                ->delete();
        }

        $deletedSms = 0;
        $smsTable = null;
        if (Schema::hasTable('sms_logs')) {
            $smsTable = 'sms_logs';
        } elseif (Schema::hasTable('smslogs')) {
            $smsTable = 'smslogs';
        } elseif (Schema::hasTable('s_m_slogs')) {
            $smsTable = 's_m_slogs';
        }

        if ($smsTable) {
            foreach (['initiated_time', 'created_at', 'updated_at'] as $dateColumn) {
                if (Schema::hasColumn($smsTable, $dateColumn)) {
                    $deletedSms = DB::table($smsTable)
                        ->whereDate($dateColumn, '<', $cutoffDate)
                        ->delete();
                    break;
                }
            }
        }

        return redirect()
            ->route('admin.stores.show', [
                'code' => $store->code,
                'start_date' => $request->query('start_date'),
                'end_date' => $request->query('end_date'),
            ])
            ->with('status', "Purge completed (before {$cutoffDate}). Deleted events: {$deletedEvents}, sms logs: {$deletedSms}, memos: {$deletedMemos}.");
    }

    public function updateApiKey(Request $request, string $code)
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if (! $store) {
            abort(404, 'Store not found');
        }

        $hasApiKeyHash = Schema::connection('central')->hasColumn('stores', 'api_key_hash');
        $hasApiKeyEnabled = Schema::connection('central')->hasColumn('stores', 'api_key_enabled');
        $hasApiKeyRotatedAt = Schema::connection('central')->hasColumn('stores', 'api_key_rotated_at');
        $hasApiKeyLast4 = Schema::connection('central')->hasColumn('stores', 'api_key_last4');

        if (! $hasApiKeyHash) {
            return redirect()
                ->route('admin.stores.show', [
                    'code' => $store->code,
                    'start_date' => $request->query('start_date'),
                    'end_date' => $request->query('end_date'),
                ])
                ->withErrors(['api_key' => "Missing column 'api_key_hash' in central stores table. Please run DB update SQL first."]);
        }

        $request->validate([
            'api_key' => ['nullable', 'string', 'min:16', 'max:128'],
            'api_key_enabled' => ['nullable', 'in:0,1'],
            'action' => ['nullable', 'in:generate'],
        ]);

        $plainKey = trim((string) $request->input('api_key', ''));
        if ($request->input('action') === 'generate' && $plainKey === '') {
            $plainKey = Str::lower($store->code) . '_live_' . Str::random(32);
        }
        $isGenerateAction = $request->input('action') === 'generate';

        $update = [];
        if ($hasApiKeyEnabled) {
            $update['api_key_enabled'] = (int) $request->input('api_key_enabled', 1) === 1 ? 1 : 0;
        }

        if ($plainKey !== '') {
            $update['api_key_hash'] = hash('sha256', $plainKey);
            if ($hasApiKeyLast4) {
                $update['api_key_last4'] = substr($plainKey, -4);
            }
            if ($hasApiKeyRotatedAt) {
                $update['api_key_rotated_at'] = now();
            }
        }

        if (! empty($update)) {
            DB::connection('central')
                ->table('stores')
                ->where('id', $store->id)
                ->update($update);
        }

        return redirect()
            ->route('admin.stores.show', [
                'code' => $store->code,
                'start_date' => $request->query('start_date'),
                'end_date' => $request->query('end_date'),
            ])
            ->with('status', $isGenerateAction && $plainKey !== ''
                ? "API key generated. Plain key: {$plainKey}"
                : 'API key settings updated.');
    }

    public function updateSmsTemplates(Request $request, string $code)
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if (! $store) {
            abort(404, 'Store not found');
        }

        $request->validate([
            'sms_tpl_welcome' => ['nullable', 'string', 'max:3000'],
            'sms_tpl_notice' => ['nullable', 'string', 'max:3000'],
            'sms_tpl_confirm' => ['nullable', 'string', 'max:3000'],
            'sms_tpl_cancel' => ['nullable', 'string', 'max:3000'],
        ]);

        $columnMap = [
            'sms_tpl_welcome',
            'sms_tpl_notice',
            'sms_tpl_confirm',
            'sms_tpl_cancel',
        ];
        $update = [];
        foreach ($columnMap as $column) {
            if (Schema::connection('central')->hasColumn('stores', $column)) {
                $update[$column] = trim((string) $request->input($column, ''));
            }
        }

        if (! empty($update)) {
            DB::connection('central')
                ->table('stores')
                ->where('id', $store->id)
                ->update($update);
        }

        return redirect()
            ->route('admin.stores.show', [
                'code' => $store->code,
                'start_date' => $request->query('start_date'),
                'end_date' => $request->query('end_date'),
            ])
            ->with('status', 'SMS templates updated.');
    }

    public function reservations(Request $request, string $code)
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if (! $store) {
            abort(404, 'Store not found');
        }

        StoreContext::applyForStore($store->code);

        $startDate = $request->query('start_date', now()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $events = Event::query()
            ->whereDate('start', '>=', $startDate)
            ->whereDate('start', '<=', $endDate)
            ->orderByDesc('start')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.stores.reservations', [
            'store' => $store,
            'events' => $events,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'user' => auth('store')->user(),
        ]);
    }

    public function reservationShow(Request $request, string $code, int $id)
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if (! $store) {
            abort(404, 'Store not found');
        }

        StoreContext::applyForStore($store->code);

        $event = Event::findOrFail($id);
        $table = null;
        if (Schema::hasTable('sms_logs')) {
            $table = 'sms_logs';
        } elseif (Schema::hasTable('smslogs')) {
            $table = 'smslogs';
        } elseif (Schema::hasTable('s_m_slogs')) {
            $table = 's_m_slogs';
        }

        $smsLogs = collect();
        if ($table) {
            $smsLogs = DB::table($table)
                ->where('event_id', $event->id)
                ->orderByDesc('id')
                ->get();
        }

        return view('admin.stores.reservation', [
            'store' => $store,
            'event' => $event,
            'smsLogs' => $smsLogs,
            'smsTableName' => $table,
            'user' => auth('store')->user(),
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'page' => $request->query('page'),
        ]);
    }

    public function smsLogs(Request $request, string $code)
    {
        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if (! $store) {
            abort(404, 'Store not found');
        }

        StoreContext::applyForStore($store->code);

        $startDate = $request->query('start_date', now()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $table = null;
        if (Schema::hasTable('sms_logs')) {
            $table = 'sms_logs';
        } elseif (Schema::hasTable('smslogs')) {
            $table = 'smslogs';
        } elseif (Schema::hasTable('s_m_slogs')) {
            $table = 's_m_slogs';
        }

        $logs = collect();
        if ($table) {
            $query = DB::table($table)
                ->whereDate('initiated_time', '>=', $startDate)
                ->whereDate('initiated_time', '<=', $endDate)
                ->orderByDesc('id');
            $logs = $query->paginate(20)->withQueryString();
        }

        return view('admin.stores.smslogs', [
            'store' => $store,
            'logs' => $logs,
            'user' => auth('store')->user(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tableName' => $table,
        ]);
    }
}

