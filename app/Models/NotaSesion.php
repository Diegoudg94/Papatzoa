<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaSesion extends Model
{
    protected $table = 'notas_sesion';

    protected $fillable = [
        'cita_id',
        'paciente_id',
        'terapeuta_id',
        'nota_encrypted',
    ];
}