<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'terapeuta_id',
        'fecha',
        'hora',
        'motivo',
        'motivo_encrypted',
        'estado',
        'comentario_terapeuta',
        'starts_at',
        'ends_at',
        'timezone',
        'duration_minutes',
        'modalidad',
        'requested_at',
        'confirmed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'requested_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function terapeuta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'terapeuta_id');
    }
}
