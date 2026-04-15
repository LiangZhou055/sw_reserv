<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Services\StoreContext;
use App\Services\ReservationLimitService;
use App\Models\StoreUser;
use App\Models\Device;
use App\Models\Event;
use App\Models\Meal;
use App\Models\ReservationSlotLimit;
use App\Models\ReservationSlotWeekdayLimit;

class StoreAuthController extends Controller
{
    private ReservationLimitService $limitService;

    public function __construct(ReservationLimitService $limitService)
    {
        $this->limitService = $limitService;
    }

    public function showLoginForm(string $storeCode)
    {
        // Ensure store context is applied (IdentifyStore already ran in web group).
        StoreContext::applyForStore($storeCode);

        if (Auth::guard('store')->check()) {
            return redirect()->route('store.dashboard', ['storeCode' => $storeCode]);
        }

        return view('store.auth.login', [
            'storeCode' => $storeCode,
        ]);
    }

    public function login(Request $request, string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        $storeCodeNormalized = strtolower(trim($storeCode));

        // Debug log for login flow
        $centralConn = DB::connection('central');
        Log::info('Store login attempt', [
            'store_code' => $storeCodeNormalized,
            'email' => $credentials['email'],
            'central_db' => $centralConn->getDatabaseName(),
        ]);

        $user = StoreUser::where('store_code', $storeCodeNormalized)
            ->where('email', $credentials['email'])
            ->first();

        Log::info('Store login user lookup result', [
            'found' => $user !== null,
            'user_id' => $user->id ?? null,
            'user_store_code' => $user->store_code ?? null,
            'user_email' => $user->email ?? null,
            'is_active' => $user->is_active ?? null,
        ]);

        $passwordOk = $user ? Hash::check($credentials['password'], $user->password) : false;
        Log::info('Store login password check', [
            'store_code' => $storeCodeNormalized,
            'email' => $credentials['email'],
            'password_ok' => $passwordOk,
        ]);

        if ($user && $user->is_active && $passwordOk) {
            Auth::guard('store')->login($user, $remember);
            $request->session()->regenerate();

            return redirect()->route('store.dashboard', ['storeCode' => $storeCode]);
        }

        return back()->withErrors([
            'email' => __('These credentials do not match our records.'),
        ])->onlyInput('email');
    }

    public function logout(Request $request, string $storeCode)
    {
        Auth::guard('store')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.login', ['storeCode' => $storeCode]);
    }

    public function dashboard(string $storeCode)
    {
        StoreContext::applyForStore($storeCode);
        $request = request();
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());
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

        $dailyAll = (clone $base)
            ->selectRaw('DATE(start) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        $dailyCanceled = (clone $base)
            ->where('status', Event::STATUS_CANCEL)
            ->selectRaw('DATE(start) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        $labels = [];
        $allSeries = [];
        $cancelSeries = [];
        foreach (CarbonPeriod::create(Carbon::parse($startDate), Carbon::parse($endDate)) as $day) {
            $key = $day->toDateString();
            $labels[] = $key;
            $allSeries[] = (int) ($dailyAll[$key] ?? 0);
            $cancelSeries[] = (int) ($dailyCanceled[$key] ?? 0);
        }

        return view('store.dashboard', [
            'storeCode' => $storeCode,
            'user' => Auth::guard('store')->user(),
            'storeInfo' => $this->storeInfo($storeCode),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalReservations' => array_sum($allSeries),
            'waitingCount' => (int) ($statusCounts[Event::STATUS_WAITING] ?? 0),
            'canceledCount' => (int) ($statusCounts[Event::STATUS_CANCEL] ?? 0),
            'dailyLabels' => $labels,
            'dailyAllValues' => $allSeries,
            'dailyCanceledValues' => $cancelSeries,
        ]);
    }

    public function devices(string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

        $devices = Device::query()
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('store.devices.index', [
            'storeCode' => $storeCode,
            'user' => Auth::guard('store')->user(),
            'storeInfo' => $this->storeInfo($storeCode),
            'devices' => $devices,
        ]);
    }

    public function updateDeviceStatus(Request $request, string $storeCode, int $id)
    {
        StoreContext::applyForStore($storeCode);

        $data = $request->validate([
            'status' => ['required', 'in:0,1'],
        ]);

        $device = Device::findOrFail($id);
        $device->status = (int) $data['status'];
        $device->save();

        return redirect()
            ->route('store.devices.index', ['storeCode' => $storeCode])
            ->with('status', $device->status === 1 ? 'Device activated.' : 'Device deactivated.');
    }

    public function reservations(Request $request, string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

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

        return view('store.reservations.index', [
            'storeCode' => $storeCode,
            'user' => Auth::guard('store')->user(),
            'storeInfo' => $this->storeInfo($storeCode),
            'events' => $events,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function reservationShow(Request $request, string $storeCode, int $id)
    {
        StoreContext::applyForStore($storeCode);

        $event = Event::findOrFail($id);
        $table = $this->resolveSmsTable();
        $smsLogs = collect();

        if ($table) {
            $smsLogs = DB::table($table)
                ->where('event_id', $event->id)
                ->orderByDesc('id')
                ->get();
        }

        return view('store.reservations.show', [
            'storeCode' => $storeCode,
            'user' => Auth::guard('store')->user(),
            'storeInfo' => $this->storeInfo($storeCode),
            'event' => $event,
            'smsLogs' => $smsLogs,
            'smsTableName' => $table,
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'page' => $request->query('page'),
        ]);
    }

    public function smsLogs(Request $request, string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

        $startDate = $request->query('start_date', now()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $table = $this->resolveSmsTable();
        $logs = collect();

        if ($table) {
            $logs = DB::table($table)
                ->whereDate('initiated_time', '>=', $startDate)
                ->whereDate('initiated_time', '<=', $endDate)
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString();
        }

        return view('store.smslogs.index', [
            'storeCode' => $storeCode,
            'user' => Auth::guard('store')->user(),
            'storeInfo' => $this->storeInfo($storeCode),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tableName' => $table,
            'logs' => $logs,
        ]);
    }

    public function mealSetup(Request $request, string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

        $weekday = (int) $request->query('weekday', now()->dayOfWeekIso);
        if ($weekday < 1 || $weekday > 7) {
            $weekday = now()->dayOfWeekIso;
        }

        $meals = Meal::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        $slots = [];
        foreach ($meals as $meal) {
            $slots[$meal->id] = $this->limitService->buildSlotsForMeal($meal);
        }

        $hasWeekdayTemplate = Schema::hasTable('reservation_slot_weekday_limits');
        $limits = collect();
        if ($hasWeekdayTemplate) {
            $limits = ReservationSlotWeekdayLimit::query()
                ->where('weekday', $weekday)
                ->whereIn('meal_id', $meals->pluck('id'))
                ->get()
                ->keyBy(function ($row) {
                    return $row->meal_id.'|'.substr($row->slot_time, 0, 5).'|'.$row->source;
                });
        }

        return view('store.setup.meal_limits', [
            'storeCode' => $storeCode,
            'user' => Auth::guard('store')->user(),
            'storeInfo' => $this->storeInfo($storeCode),
            'weekday' => $weekday,
            'meals' => $meals,
            'slots' => $slots,
            'limits' => $limits,
            'hasWeekdayTemplate' => $hasWeekdayTemplate,
        ]);
    }

    public function mealManagement(string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

        $meals = Meal::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('store.setup.meals', [
            'storeCode' => $storeCode,
            'user' => Auth::guard('store')->user(),
            'storeInfo' => $this->storeInfo($storeCode),
            'meals' => $meals,
        ]);
    }

    public function mealSetupSave(Request $request, string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

        if (! Schema::hasTable('reservation_slot_weekday_limits')) {
            return back()->withErrors([
                'meal_setup' => 'Missing table reservation_slot_weekday_limits. Please run the SQL update first.',
            ]);
        }

        $data = $request->validate([
            'weekday' => ['required', 'integer', 'between:1,7'],
            'limits' => ['required', 'array'],
            'apply_all_weekdays' => ['nullable', 'in:0,1'],
        ]);

        $targetWeekdays = ((int) ($data['apply_all_weekdays'] ?? 0) === 1)
            ? [1, 2, 3, 4, 5, 6, 7]
            : [(int) $data['weekday']];

        foreach ($data['limits'] as $key => $row) {
            $parts = explode('|', (string) $key);
            if (count($parts) !== 3) {
                continue;
            }
            [$mealId, $slotTime, $source] = $parts;
            if ((int) $source !== Event::SOURCE_CUSTOMER) {
                continue;
            }

            foreach ($targetWeekdays as $wd) {
                ReservationSlotWeekdayLimit::query()->updateOrCreate(
                    [
                        'weekday' => (int) $wd,
                        'meal_id' => (int) $mealId,
                        'slot_time' => $slotTime.':00',
                        'source' => (int) $source,
                    ],
                    [
                        'max_reservations' => max((int) ($row['max_reservations'] ?? 10), 0),
                        'max_persons' => max((int) ($row['max_persons'] ?? 30), 0),
                        'is_enabled' => isset($row['is_enabled']) ? 1 : 0,
                    ]
                );
            }
        }

        return redirect()
            ->route('store.reservation_slot_limits.index', ['storeCode' => $storeCode, 'weekday' => $data['weekday']])
            ->with('status', ((int) ($data['apply_all_weekdays'] ?? 0) === 1)
                ? 'Reservation slot limits updated for all weekdays.'
                : 'Reservation slot limits updated for selected weekday.');
    }

    public function mealStore(Request $request, string $storeCode)
    {
        StoreContext::applyForStore($storeCode);

        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:20'],
            'name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        if ($data['start_time'] >= $data['end_time']) {
            return back()->withErrors(['start_time' => 'Start time must be earlier than end time'])->withInput();
        }

        Meal::create([
            'code' => strtolower($data['code']),
            'name' => $data['name'],
            'start_time' => $data['start_time'].':00',
            'end_time' => $data['end_time'].':00',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (int) ($data['is_active'] ?? 1) === 1 ? 1 : 0,
        ]);

        return redirect()
            ->route('store.meals.index', ['storeCode' => $storeCode])
            ->with('status', 'Meal created.');
    }

    public function mealUpdate(Request $request, string $storeCode, int $id)
    {
        StoreContext::applyForStore($storeCode);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        if ($data['start_time'] >= $data['end_time']) {
            return back()->withErrors(['start_time' => 'Start time must be earlier than end time'])->withInput();
        }

        $meal = Meal::findOrFail($id);
        $meal->update([
            'name' => $data['name'],
            'start_time' => $data['start_time'].':00',
            'end_time' => $data['end_time'].':00',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (int) ($data['is_active'] ?? 1) === 1 ? 1 : 0,
        ]);

        return redirect()
            ->route('store.meals.index', ['storeCode' => $storeCode])
            ->with('status', 'Meal updated.');
    }

    public function mealDelete(Request $request, string $storeCode, int $id)
    {
        StoreContext::applyForStore($storeCode);

        $meal = Meal::findOrFail($id);
        $meal->delete();

        return redirect()
            ->route('store.meals.index', ['storeCode' => $storeCode])
            ->with('status', 'Meal deleted.');
    }

    private function storeInfo(string $storeCode)
    {
        return DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower(trim($storeCode))])
            ->first();
    }

    private function resolveSmsTable(): ?string
    {
        if (Schema::hasTable('sms_logs')) {
            return 'sms_logs';
        }
        if (Schema::hasTable('smslogs')) {
            return 'smslogs';
        }
        if (Schema::hasTable('s_m_slogs')) {
            return 's_m_slogs';
        }

        return null;
    }
}

