<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistAvailabilitySetting extends Model
{
    protected $fillable = [
        'therapist_id',
        'timezone',
        'default_duration_minutes',
        'buffer_before_minutes',
        'buffer_after_minutes',
        'minimum_notice_hours',
        'maximum_booking_days',
        'requires_confirmation',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_confirmation' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }
}
