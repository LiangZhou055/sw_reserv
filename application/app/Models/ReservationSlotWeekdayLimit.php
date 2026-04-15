<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSlotWeekdayLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekday',
        'meal_id',
        'slot_time',
        'source',
        'max_reservations',
        'max_persons',
        'is_enabled',
    ];
}
