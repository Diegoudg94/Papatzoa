<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // ID del terapeuta vinculado
            $table->unsignedBigInteger('terapeuta_id')
                ->nullable();

            // Motivo inicial del paciente
            $table->text('motivo_terapia')
                ->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'terapeuta_id',
                'motivo_terapia',
            ]);
        });
    }
};
