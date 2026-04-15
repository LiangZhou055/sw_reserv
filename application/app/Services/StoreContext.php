<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreContext
{
    /**
     * Identify current store using explicit store code only.
     */
    public static function resolveStoreCode(Request $request): ?string
    {
        $storeCode = $request->header('X-Store-Code');
        if (!empty($storeCode)) {
            return strtolower(trim($storeCode));
        }

        $route = $request->route();
        if ($route !== null) {
            $fromRoute = $route->parameter('storeCode');
            if ($fromRoute !== null && $fromRoute !== '') {
                return strtolower(trim((string) $fromRoute));
            }
        }

        $storeCode = $request->input('store');
        if (!empty($storeCode)) {
            return strtolower(trim((string) $storeCode));
        }

        return Config::get('stores.default_store');
    }

    /**
     * Apply per-store DB and Twilio config.
     */
    public static function applyForStore(?string $storeCode): bool
    {
        if (empty($storeCode)) {
            return false;
        }

        $centralStore = self::getStoreFromCentralByCode($storeCode);
        if (!empty($centralStore)) {
            $dbApplied = self::applyDatabaseConfig($storeCode, [
                'host' => $centralStore->db_host,
                'port' => $centralStore->db_port,
                'database' => $centralStore->db_database,
                'username' => $centralStore->db_username,
                'password' => $centralStore->db_password,
            ]);
            $twilioApplied = self::applyTwilioConfig($storeCode, [
                'from' => $centralStore->twilio_from,
            ]);

            app()->instance('store.code', $storeCode);
            app()->instance('store.rest_prefix', $centralStore->rest_prefix ?? null);
            app()->instance('store.rest_name', $centralStore->rest_name ?? null);
            return $dbApplied || $twilioApplied;
        }

        // Legacy/local fallback (kept for safe rollout)
        $legacyTenant = Config::get("stores.tenants.{$storeCode}");
        if (empty($legacyTenant) || !is_array($legacyTenant)) {
            Log::warning("StoreContext: store '{$storeCode}' not found in central or local config.");
            return false;
        }

        $dbApplied = self::applyDatabaseConfig($storeCode, $legacyTenant['database'] ?? []);
        $twilioApplied = self::applyTwilioConfig($storeCode, $legacyTenant['twilio'] ?? []);

        app()->instance('store.code', $storeCode);
        app()->instance('store.rest_prefix', $legacyTenant['rest_prefix'] ?? null);
        app()->instance('store.rest_name', $legacyTenant['rest_name'] ?? null);

        return $dbApplied || $twilioApplied;
    }

    public static function getRestPrefix(): string
    {
        return (string) (app()->bound('store.rest_prefix')
            ? app('store.rest_prefix')
            : '');
    }

    public static function getRestName(): string
    {
        return (string) (app()->bound('store.rest_name')
            ? app('store.rest_name')
            : '');
    }

    protected static function getStoreFromCentralByCode(string $storeCode)
    {
        try {
            return DB::connection('central')
                ->table('stores')
                ->where('is_active', 1)
                ->whereRaw('LOWER(code) = ?', [strtolower($storeCode)])
                ->first();
        } catch (\Throwable $e) {
            Log::warning("StoreContext: central lookup failed for '{$storeCode}': ".$e->getMessage());
            return null;
        }
    }

    protected static function applyDatabaseConfig(string $storeCode, array $database): bool
    {
        if (empty($database)) {
            return false;
        }

        $required = ['database', 'username'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $database) || $database[$field] === '') {
                Log::warning("StoreContext: missing db field '{$field}' for store '{$storeCode}'.");
                return false;
            }
        }

        $current = Config::get('database.connections.mysql');
        if (!is_array($current)) {
            return false;
        }

        $newConnection = array_merge($current, [
            'host' => $database['host'] ?? $current['host'],
            'port' => (string)($database['port'] ?? $current['port']),
            'database' => $database['database'],
            'username' => $database['username'],
            'password' => $database['password'] ?? '',
        ]);

        try {
            DB::purge('mysql');
            Config::set('database.connections.mysql', $newConnection);
            DB::reconnect('mysql');
            DB::connection('mysql')->getPdo();
            return true;
        } catch (\Throwable $e) {
            // Revert to last known connection config to reduce risk.
            Config::set('database.connections.mysql', $current);
            DB::purge('mysql');
            DB::reconnect('mysql');
            Log::error("StoreContext: failed db switch for store '{$storeCode}': ".$e->getMessage());
            return false;
        }
    }

    protected static function applyTwilioConfig(string $storeCode, array $twilio): bool
    {
        if (empty($twilio)) {
            return false;
        }

        $required = ['from'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $twilio) || $twilio[$field] === '') {
                Log::warning("StoreContext: missing twilio field '{$field}' for store '{$storeCode}'.");
                return false;
            }
        }

        // Keep global SID/token from env, switch only sender number per store.
        Config::set('services.twilio.from', $twilio['from']);

        return true;
    }
}
