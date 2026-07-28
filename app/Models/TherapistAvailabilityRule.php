<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistAvailabilityRule extends Model
{
    protected $fillable = [
        'therapist_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }
}
