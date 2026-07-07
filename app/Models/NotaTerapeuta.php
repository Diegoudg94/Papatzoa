<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaTerapeuta extends Model
{
    protected $table = 'notas_terapeuta';

    protected $fillable = [
        'paciente_id',
        'terapeuta_id',
        'nota_encrypted',
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
