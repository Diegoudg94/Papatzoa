<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nombre', 'correo', 'password', 'terapeuta'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function diarioEmociones(): HasMany
    {
        return $this->hasMany(DiarioEmocion::class);
    }

    public function citasComoPaciente(): HasMany
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    public function notasComoPaciente(): HasMany
    {
        return $this->hasMany(NotaTerapeuta::class, 'paciente_id');
    }

    public function pacientesAsignados(): HasMany
    {
        return $this->hasMany(User::class, 'terapeuta_id');
    }

    public function therapistCredentials(): HasMany
    {
        return $this->hasMany(TherapistCredential::class, 'terapeuta_id');
    }
}
