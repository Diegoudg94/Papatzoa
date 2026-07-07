<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistCredential extends Model
{
    protected $table = 'therapist_credentials';

    protected $fillable = [
        'terapeuta_id',
        'tipo_documento',
        'archivo_path',
        'nombre_original',
        'estado',
        'comentario_revision',
    ];
}