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
        'motivo_encrypted',
        'estado',
        'comentario_terapeuta',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function terapeuta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'terapeuta_id');
    }
}
