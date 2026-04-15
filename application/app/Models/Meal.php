<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'start_time',
        'end_time',
        'sort_order',
        'is_active',
    ];
}
