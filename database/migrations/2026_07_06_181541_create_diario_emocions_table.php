<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diario_emociones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Datos no cifrados para mostrar/filtrar rápido
            $table->string('emocion')->nullable();
            $table->integer('intensidad')->nullable();

            // Datos sensibles cifrados
            $table->text('situacion_encrypted')->nullable();
            $table->text('pensamiento_encrypted')->nullable();
            $table->text('conducta_encrypted')->nullable();
            $table->text('interpretacion_encrypted')->nullable();
            $table->text('reestructuracion_encrypted')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diario_emociones');
    }
};