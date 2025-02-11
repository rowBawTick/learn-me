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
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('price_per_hour', 8, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes for efficient searching and sorting
            $table->index('price_per_hour');
            $table->index('is_active');
            $table->index(['user_id', 'is_active']);
            $table->index(['subject_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};
