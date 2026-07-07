<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            if (!Schema::hasColumn('citas', 'motivo_encrypted')) {
                $table->text('motivo_encrypted')->nullable()->after('hora');
            }

            if (Schema::hasColumn('citas', 'terapeuta_id')) {
                $table->unsignedBigInteger('terapeuta_id')->nullable()->change();
            }
        });

        if (Schema::hasColumn('citas', 'motivo')) {
            DB::table('citas')
                ->whereNull('motivo_encrypted')
                ->whereNotNull('motivo')
                ->update([
                    'motivo_encrypted' => DB::raw('motivo'),
                ]);
        }

        Schema::create('notas_terapeuta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('terapeuta_id')->constrained('users')->cascadeOnDelete();
            $table->text('nota_encrypted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_terapeuta');

        Schema::table('citas', function (Blueprint $table) {
            if (Schema::hasColumn('citas', 'motivo_encrypted')) {
                $table->dropColumn('motivo_encrypted');
            }

            if (Schema::hasColumn('citas', 'terapeuta_id')) {
                $table->unsignedBigInteger('terapeuta_id')->nullable(false)->change();
            }
        });
    }
};
