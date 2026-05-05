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
        Schema::create('restaurant_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('weight')->default(0);
            $table->boolean('is_24hr')->default(false);
            $table->boolean('is_close')->default(false);
            $table->time('period_1_start')->nullable();
            $table->time('period_1_end')->nullable();
            $table->time('period_2_start')->nullable();
            $table->time('period_2_end')->nullable();
            $table->time('period_3_start')->nullable();
            $table->time('period_3_end')->nullable();
            $table->timestamps();

            $table->index(['day_of_week', 'weight', 'is_close', 'is_24hr']);
            $table->index('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_hours');
    }
};
