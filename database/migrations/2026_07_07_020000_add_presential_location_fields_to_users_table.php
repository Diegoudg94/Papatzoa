<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pais_atencion')) {
                $table->string('pais_atencion', 100)->nullable();
            }

            if (!Schema::hasColumn('users', 'estado_atencion')) {
                $table->string('estado_atencion', 100)->nullable();
            }

            if (!Schema::hasColumn('users', 'ciudad_atencion')) {
                $table->string('ciudad_atencion', 100)->nullable();
            }

            if (!Schema::hasColumn('users', 'direccion_atencion')) {
                $table->string('direccion_atencion', 255)->nullable();
            }

            if (!Schema::hasColumn('users', 'codigo_postal_atencion')) {
                $table->string('codigo_postal_atencion', 20)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'pais_atencion',
                'estado_atencion',
                'ciudad_atencion',
                'direccion_atencion',
                'codigo_postal_atencion',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
