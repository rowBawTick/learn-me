<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailableTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'advert_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_recurring',
    ];

    public function advert(): BelongsTo
    {
        return $this->belongsTo(Advert::class);
    }
}
