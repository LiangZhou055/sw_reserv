<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_id',
        'slot_time',
        'is_active',
    ];
}
