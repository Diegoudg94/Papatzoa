<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguimientoEmocion extends Model
{
    protected $table = 'seguimientos_emocion';

    protected $fillable = [
        'diario_emocion_id',
        'user_id',
        'nota_encrypted',
    ];

    public function diarioEmocion(): BelongsTo
    {
        return $this->belongsTo(DiarioEmocion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
