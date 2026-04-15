<?php

namespace App\Http\Middleware;

use App\Services\StoreContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnsureStoreApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $storeCode = strtolower(trim((string) $request->route('storeCode')));
        if ($storeCode === '') {
            return response()->json(['error' => 'Missing store code'], 400);
        }

        $apiKey = (string) ($request->header('X-Api-Key') ?? '');
        if ($apiKey === '') {
            $auth = (string) ($request->header('Authorization') ?? '');
            if (preg_match('/^\s*Bearer\s+(.+?)\s*$/i', $auth, $m)) {
                $apiKey = trim((string) ($m[1] ?? ''));
            }
        }
        if ($apiKey === '') {
            return response()->json(['error' => 'Missing API key'], 401);
        }

        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [$storeCode])
            ->where('is_active', 1)
            ->first();

        if (! $store) {
            return response()->json(['error' => 'Store not found or inactive'], 404);
        }

        if ((int) ($store->api_key_enabled ?? 0) !== 1 || empty($store->api_key_hash)) {
            return response()->json(['error' => 'API key is disabled'], 403);
        }

        if (! hash_equals((string) $store->api_key_hash, hash('sha256', $apiKey))) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        StoreContext::applyForStore($storeCode);
        $request->attributes->set('store_code', $storeCode);

        return $next($request);
    }
}
