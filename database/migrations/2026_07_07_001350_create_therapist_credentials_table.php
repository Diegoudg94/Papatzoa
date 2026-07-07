<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapist_credentials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('terapeuta_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('tipo_documento');
            $table->string('archivo_path');
            $table->string('nombre_original')->nullable();
            $table->string('estado')->default('pendiente');
            $table->text('comentario_revision')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapist_credentials');
    }
};