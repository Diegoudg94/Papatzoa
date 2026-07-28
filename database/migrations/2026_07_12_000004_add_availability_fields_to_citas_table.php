<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            if (!Schema::hasColumn('citas', 'starts_at')) {
                $table->timestampTz('starts_at')->nullable();
            }

            if (!Schema::hasColumn('citas', 'ends_at')) {
                $table->timestampTz('ends_at')->nullable();
            }

            if (!Schema::hasColumn('citas', 'timezone')) {
                $table->string('timezone')->nullable();
            }

            if (!Schema::hasColumn('citas', 'duration_minutes')) {
                $table->unsignedSmallInteger('duration_minutes')->nullable();
            }

            if (!Schema::hasColumn('citas', 'modalidad')) {
                $table->string('modalidad')->nullable();
            }

            if (!Schema::hasColumn('citas', 'requested_at')) {
                $table->timestampTz('requested_at')->nullable();
            }

            if (!Schema::hasColumn('citas', 'confirmed_at')) {
                $table->timestampTz('confirmed_at')->nullable();
            }

            if (!Schema::hasColumn('citas', 'cancelled_at')) {
                $table->timestampTz('cancelled_at')->nullable();
            }
        });

        if (
            Schema::hasColumn('citas', 'terapeuta_id')
            && Schema::hasColumn('citas', 'starts_at')
            && Schema::hasColumn('citas', 'estado')
        ) {
            Schema::table('citas', function (Blueprint $table) {
                $table->index(['terapeuta_id', 'starts_at', 'estado'], 'citas_terapeuta_starts_estado_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            if (Schema::hasColumn('citas', 'terapeuta_id') && Schema::hasColumn('citas', 'starts_at') && Schema::hasColumn('citas', 'estado')) {
                $table->dropIndex('citas_terapeuta_starts_estado_idx');
            }

            $columns = [
                'starts_at',
                'ends_at',
                'timezone',
                'duration_minutes',
                'modalidad',
                'requested_at',
                'confirmed_at',
                'cancelled_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('citas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
