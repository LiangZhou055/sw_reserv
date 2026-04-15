<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-store runtime configuration
    |--------------------------------------------------------------------------
    |
    | STORE_TENANTS expects a JSON object. Example:
    | {
    |   "store-a": {
    |     "hosts": ["store-a.example.com", "store-a.local"],
    |     "database": {
    |       "host": "127.0.0.1",
    |       "port": "3306",
    |       "database": "reserve_store_a",
    |       "username": "root",
    |       "password": ""
    |     },
    |     "twilio": {
    |       "account_sid": "ACxxxx",
    |       "auth_token": "xxxx",
    |       "from": "+15555555555"
    |     }
    |   }
    | }
    |
    */
    'default_store' => env('DEFAULT_STORE_CODE'),

    'tenants' => json_decode(env('STORE_TENANTS', '{}'), true) ?: [],
];
