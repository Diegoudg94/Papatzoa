<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistAvailabilityException extends Model
{
    public const TYPE_BLOCKED = 'blocked';
    public const TYPE_AVAILABLE = 'available';
    public const TYPE_VACATION = 'vacation';

    protected $fillable = [
        'therapist_id',
        'exception_date',
        'start_time',
        'end_time',
        'type',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'exception_date' => 'date',
        ];
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }
}
