<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('supabase_id')->nullable()->unique()->after('id');
            $table->string('avatar_url')->nullable()->after('correo');
            $table->string('auth_provider')->nullable()->default('local')->after('avatar_url');

            $table->boolean('terapeuta_verificado')->default(false)->after('terapeuta');
            $table->string('estado_verificacion')->default('no_aplica')->after('terapeuta_verificado');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'supabase_id',
                'avatar_url',
                'auth_provider',
                'terapeuta_verificado',
                'estado_verificacion',
            ]);
        });
    }
};