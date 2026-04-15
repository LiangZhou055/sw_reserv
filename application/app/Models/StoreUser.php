<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class StoreUser extends Authenticatable
{
    use Notifiable;

    protected $connection = 'central';

    protected $table = 'users';

    protected $fillable = [
        'store_code',
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

