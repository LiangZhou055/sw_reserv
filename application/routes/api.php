<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerReservationApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['store.api_key'])->group(function () {
    Route::get('/v1/stores/{storeCode}/availability', [CustomerReservationApiController::class, 'availability']);
    Route::get('/v1/stores/{storeCode}/reservations', [CustomerReservationApiController::class, 'index']);
    Route::post('/v1/stores/{storeCode}/reservations', [CustomerReservationApiController::class, 'store']);
    Route::post('/v1/stores/{storeCode}/reservations/{id}/cancel', [CustomerReservationApiController::class, 'cancel']);
});
