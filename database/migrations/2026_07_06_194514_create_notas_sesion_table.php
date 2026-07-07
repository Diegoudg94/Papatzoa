<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notas_sesion')) {
            return;
        }

        Schema::create('notas_sesion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cita_id')
                ->constrained('citas')
                ->cascadeOnDelete();

            $table->foreignId('paciente_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('terapeuta_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('nota_encrypted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_sesion');
    }
};
