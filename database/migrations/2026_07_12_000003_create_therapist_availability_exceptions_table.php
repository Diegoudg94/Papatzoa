<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapist_availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('therapist_id');
            $table->date('exception_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('type');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['therapist_id', 'exception_date']);

            $table->foreign('therapist_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapist_availability_exceptions');
    }
};
