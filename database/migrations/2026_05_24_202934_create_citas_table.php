<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('paciente_id');
            $table->unsignedBigInteger('terapeuta_id');

            $table->date('fecha');
            $table->time('hora');

            $table->text('motivo')->nullable();

            $table->string('estado')->default('pendiente');
            // pendiente / aceptada / rechazada

            $table->text('comentario_terapeuta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};