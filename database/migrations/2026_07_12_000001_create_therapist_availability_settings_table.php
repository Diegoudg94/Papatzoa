<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapist_availability_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('therapist_id')->unique();
            $table->string('timezone')->default('America/Mexico_City');
            $table->unsignedSmallInteger('default_duration_minutes')->default(60);
            $table->unsignedSmallInteger('buffer_before_minutes')->default(0);
            $table->unsignedSmallInteger('buffer_after_minutes')->default(0);
            $table->unsignedSmallInteger('minimum_notice_hours')->default(24);
            $table->unsignedSmallInteger('maximum_booking_days')->default(60);
            $table->boolean('requires_confirmation')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('therapist_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapist_availability_settings');
    }
};
