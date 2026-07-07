<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos_emocion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('diario_emocion_id')
                ->constrained('diario_emociones')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Nota sensible cifrada
            $table->text('nota_encrypted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos_emocion');
    }
};