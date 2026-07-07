<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono')->nullable();
            }

            if (!Schema::hasColumn('users', 'especialidad')) {
                $table->string('especialidad')->nullable();
            }

            if (!Schema::hasColumn('users', 'biografia')) {
                $table->text('biografia')->nullable();
            }

            if (!Schema::hasColumn('users', 'experiencia_anios')) {
                $table->integer('experiencia_anios')->nullable();
            }

            if (!Schema::hasColumn('users', 'cedula_profesional')) {
                $table->string('cedula_profesional')->nullable();
            }

            if (!Schema::hasColumn('users', 'institucion_formacion')) {
                $table->string('institucion_formacion')->nullable();
            }

            if (!Schema::hasColumn('users', 'enfoque_terapeutico')) {
                $table->string('enfoque_terapeutico')->nullable();
            }

            if (!Schema::hasColumn('users', 'modalidad_atencion')) {
                $table->string('modalidad_atencion')->nullable();
            }

            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable();
            }

            if (!Schema::hasColumn('users', 'estado_verificacion')) {
                $table->string('estado_verificacion')->default('no_enviada');
            }

            if (!Schema::hasColumn('users', 'terapeuta_verificado')) {
                $table->boolean('terapeuta_verificado')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'telefono',
                'especialidad',
                'biografia',
                'experiencia_anios',
                'cedula_profesional',
                'institucion_formacion',
                'enfoque_terapeutico',
                'modalidad_atencion',
                'profile_photo_path',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
