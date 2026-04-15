<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\FullCalenderController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\StoreAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminStoreController;
use App\Http\Controllers\AdminUserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::middleware(['cors'])->group(function () {
//     Route::get('/api',  [APIController::class, 'index']);
//     Route::get('/api/{sn}',  [APIController::class, 'list'])->name('dynamic.api');
//     Route::post('registration/verify', [APIController::class, 'verify'])->name('registration.verify');
//     Route::post('customer/store', [CustomerController::class, 'store'])->name('customer.store');
//     Route::post('customer/delete', [CustomerController::class, 'delete'])->name('customer.delete');
//     Route::post('customer/dinein', [CustomerController::class, 'dineIn'])->name('customer.dinein');
//     Route::post('customer/notice', [CustomerController::class, 'notice'])->name('customer.notice');
//     Route::get('customer', [CustomerController::class, 'index'])->name('customer.index');
//     Route::get('refresh', [CustomerController::class, 'refresh'])->name('customer.refresh');
//     Route::post('sms/receive', [SMSController::class, 'receiveSMS'])->name('sms.receive');
// });


// Route::get('fullcalender', [FullCalenderController::class, 'index']);
// Route::post('fullcalenderAjax', [FullCalenderController::class, 'ajax']);

Route::get('/api/{storeCode}/{apiKey}',  [APIController::class, 'listByStore'])->name('dynamic.api.store');
Route::get('/api/{sn}',  [APIController::class, 'list'])->name('dynamic.api');
Route::post('registration/verify', [APIController::class, 'verify'])->name('registration.verify');

// Super admin (core-level) login
Route::prefix('admin')->group(function () {
    Route::get('/auth', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/auth', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware(['auth:store', 'super.admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/stores', [AdminStoreController::class, 'index'])->name('admin.stores.index');
        Route::get('/stores/{code}', [AdminStoreController::class, 'show'])->name('admin.stores.show');
        Route::post('/stores/{code}/api-key', [AdminStoreController::class, 'updateApiKey'])->name('admin.stores.api_key.update');
        Route::post('/stores/{code}/sms-templates', [AdminStoreController::class, 'updateSmsTemplates'])->name('admin.stores.sms_templates.update');
        Route::post('/stores/{code}/purge', [AdminStoreController::class, 'purge'])->name('admin.stores.purge');
        Route::get('/stores/{code}/reservations', [AdminStoreController::class, 'reservations'])->name('admin.stores.reservations.index');
        Route::get('/stores/{code}/reservations/{id}', [AdminStoreController::class, 'reservationShow'])->name('admin.stores.reservations.show');
        Route::get('/stores/{code}/sms-logs', [AdminStoreController::class, 'smsLogs'])->name('admin.stores.smslogs.index');
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::post('/users/{id}/password', [AdminUserController::class, 'resetPassword'])->name('admin.users.password');
        Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggleActive'])->name('admin.users.toggle');
    });
});

Route::prefix('{storeCode}')->group(function () {
    Route::get('/auth', [StoreAuthController::class, 'showLoginForm'])->name('store.login');
    Route::post('/auth', [StoreAuthController::class, 'login'])->name('store.login.submit');

    Route::middleware('auth:store')->group(function () {
        Route::post('/logout', [StoreAuthController::class, 'logout'])->name('store.logout');
        Route::get('/dashboard', [StoreAuthController::class, 'dashboard'])->name('store.dashboard');
        Route::get('/devices', [StoreAuthController::class, 'devices'])->name('store.devices.index');
        Route::post('/devices/{id}/status', [StoreAuthController::class, 'updateDeviceStatus'])->name('store.devices.status');
        Route::get('/reservations', [StoreAuthController::class, 'reservations'])->name('store.reservations.index');
        Route::get('/reservations/{id}', [StoreAuthController::class, 'reservationShow'])->name('store.reservations.show');
        Route::get('/sms-logs', [StoreAuthController::class, 'smsLogs'])->name('store.smslogs.index');
        Route::get('/meal-setup', [StoreAuthController::class, 'mealManagement'])->name('store.meals.index');
        Route::get('/reservation-slot-limits', [StoreAuthController::class, 'mealSetup'])->name('store.reservation_slot_limits.index');
        Route::post('/reservation-slot-limits', [StoreAuthController::class, 'mealSetupSave'])->name('store.reservation_slot_limits.save');
        Route::post('/meal-setup/meals', [StoreAuthController::class, 'mealStore'])->name('store.meals.store');
        Route::post('/meal-setup/meals/{id}', [StoreAuthController::class, 'mealUpdate'])->name('store.meals.update');
        Route::post('/meal-setup/meals/{id}/delete', [StoreAuthController::class, 'mealDelete'])->name('store.meals.delete');
    });
});

Route::middleware(['calendar.device'])->group(function () {
    Route::get('fullcalender', [FullCalenderController::class, 'index'])->name('fullcalender.index');
    Route::post('fullcalenderAjax', [FullCalenderController::class, 'ajax'])->name('fullcalender.ajax');
    Route::post('search/', [FullCalenderController::class, 'search'])->name('calendar.search');
    Route::post('saveMemo/', [MemoController::class, 'save'])->name('calendar.memo.save');
    Route::post('getMemo/', [MemoController::class, 'get'])->name('calendar.memo.get');
});

Route::post('sms/receive', [SMSController::class, 'receiveSMS'])->name('sms.receive');

Route::get('{storeCode}/notice', [FullCalenderController::class, 'sendNoticeBatch'])->name('batch.notice');
Route::get('{storeCode}/cancel', [FullCalenderController::class, 'sendCancelBatch'])->name('batch.cancel');
Route::get('{storeCode}/welcome', [FullCalenderController::class, 'sendWelcomeBatch'])->name('batch.welcome');
