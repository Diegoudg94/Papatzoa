<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiarioEmocion extends Model
{
    protected $table = 'diario_emociones';

    protected $fillable = [
        'user_id',
        'emocion',
        'intensidad',
        'situacion_encrypted',
        'pensamiento_encrypted',
        'conducta_encrypted',
        'interpretacion_encrypted',
        'reestructuracion_encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoEmocion::class);
    }
}
