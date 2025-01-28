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
        Schema::create('available_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advert_id')->constrained()->onDelete('cascade');
            $table->string('day_of_week');
            $table->time('local_start_time');
            $table->time('local_end_time');
            $table->string('time_zone');
            $table->boolean('is_recurring')->default(true);
            $table->timestamps();

            // Prevent duplicate time slots for the same advert
            $table->unique(['advert_id', 'day_of_week', 'local_start_time', 'local_end_time'], 'unique_time_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_times');
    }
};
