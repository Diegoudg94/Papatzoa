<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nacionalidad')) {
                $table->string('nacionalidad')->nullable();
            }

            if (!Schema::hasColumn('users', 'telefono_lada')) {
                $table->string('telefono_lada', 10)->nullable();
            }

            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['nacionalidad', 'telefono_lada'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
