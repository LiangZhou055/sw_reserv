<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSlotLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'limit_date',
        'meal_id',
        'slot_time',
        'source',
        'max_reservations',
        'max_persons',
        'is_enabled',
    ];
}
