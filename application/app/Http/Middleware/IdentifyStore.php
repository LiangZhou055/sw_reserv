<?php

namespace App\Http\Middleware;

use App\Services\StoreContext;
use Closure;
use Illuminate\Http\Request;

class IdentifyStore
{
    public function handle(Request $request, Closure $next)
    {
        $storeCode = StoreContext::resolveStoreCode($request);

        if (!empty($storeCode)) {
            StoreContext::applyForStore($storeCode);
            $request->attributes->set('store_code', $storeCode);
        }

        return $next($request);
    }
}
