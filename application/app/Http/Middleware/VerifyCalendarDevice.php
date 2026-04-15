<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;

/**
 * Require store + device sn on calendar routes so tenant context matches an approved device.
 * Unauthenticated requests always receive JSON errors (no HTML shell without credentials).
 */
class VerifyCalendarDevice
{
    public function handle(Request $request, Closure $next)
    {
        $sn = $request->input('sn');
        $store = $request->input('store');

        if ($sn === null || $sn === '' || $store === null || $store === '') {
            return response()->json(['error' => 'Missing store or device credentials'], 403);
        }

        if (app()->bound('store.code')) {
            if (strtolower((string) $store) !== strtolower((string) app('store.code'))) {
                return response()->json(['error' => 'Store mismatch'], 403);
            }
        }

        $device = Device::where('sn', $sn)->first();
        if (! $device || (int) $device->status !== 1) {
            return response()->json(['error' => 'Invalid or inactive device'], 403);
        }

        return $next($request);
    }
}
