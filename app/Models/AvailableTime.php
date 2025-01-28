<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AvailableTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'advert_id',
        'day_of_week',
        'local_start_time',
        'local_end_time',
        'time_zone',
        'is_recurring',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
    ];

    /**
     * Format the local start time without seconds
     */
    protected function getLocalStartTimeAttribute($value)
    {
        return substr($value, 0, 5); // Returns just HH:MM
    }

    /**
     * Format the local end time without seconds
     */
    protected function getLocalEndTimeAttribute($value)
    {
        return substr($value, 0, 5); // Returns just HH:MM
    }

    /**
     * Set the local start time with seconds
     */
    protected function setLocalStartTimeAttribute($value)
    {
        $this->attributes['local_start_time'] = $value . ':00';
    }

    /**
     * Set the local end time with seconds
     */
    protected function setLocalEndTimeAttribute($value)
    {
        $this->attributes['local_end_time'] = $value . ':00';
    }

    public function advert(): BelongsTo
    {
        return $this->belongsTo(Advert::class);
    }

    /**
     * Get the local start time in a specific timezone
     */
    public function getStartTimeInTimeZone(string $targetTimeZone): string
    {
        if ($targetTimeZone === $this->time_zone) {
            return $this->local_start_time;
        }

        // Convert from source timezone to target timezone
        $time = Carbon::parse($this->local_start_time, $this->time_zone)
            ->setTimezone($targetTimeZone);
        return $time->format('H:i');
    }

    /**
     * Get the local end time in a specific timezone
     */
    public function getEndTimeInTimeZone(string $targetTimeZone): string
    {
        if ($targetTimeZone === $this->time_zone) {
            return $this->local_end_time;
        }

        $time = Carbon::parse($this->local_end_time, $this->time_zone)
            ->setTimezone($targetTimeZone);
        return $time->format('H:i');
    }
}
