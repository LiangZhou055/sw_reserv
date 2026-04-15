<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\StoreContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminReportController extends Controller
{
    public function index()
    {
        $stores = DB::connection('central')->table('stores')->orderBy('code')->get();
        $rows = [];

        foreach ($stores as $store) {
            try {
                StoreContext::applyForStore($store->code);

                $rows[] = [
                    'code' => $store->code,
                    'name' => $store->rest_name ?? '',
                    'total_events' => Event::count(),
                    'today_events' => Event::whereDate('start', now()->toDateString())->count(),
                    'today_confirmed' => Event::whereDate('start', now()->toDateString())
                        ->where('sms_status', Event::SMS_STATUS_CONFIRM)->count(),
                    'today_canceled' => Event::whereDate('start', now()->toDateString())
                        ->where('status', Event::STATUS_CANCEL)->count(),
                ];
            } catch (\Throwable $e) {
                Log::warning('Admin report failed on store', ['store' => $store->code, 'error' => $e->getMessage()]);
                $rows[] = [
                    'code' => $store->code,
                    'name' => $store->rest_name ?? '',
                    'total_events' => null,
                    'today_events' => null,
                    'today_confirmed' => null,
                    'today_canceled' => null,
                ];
            }
        }

        return view('admin.reports.index', [
            'rows' => $rows,
            'user' => auth('store')->user(),
        ]);
    }
}

