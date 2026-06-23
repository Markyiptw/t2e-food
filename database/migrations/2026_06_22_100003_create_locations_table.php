<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->foreignId('restaurant_id')
                ->primary()
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->timestamps();
        });

        DB::statement(<<<'SQL'
            INSERT INTO locations (restaurant_id, latitude, longitude, created_at, updated_at)
            SELECT
                id,
                (data->>'mapLatitude')::numeric(10, 7),
                (data->>'mapLongitude')::numeric(11, 7),
                NOW(),
                NOW()
            FROM restaurants
            WHERE data->'mapLatitude' IS NOT NULL
              AND data->'mapLongitude' IS NOT NULL
              AND (data->>'mapLatitude')::float != 0
              AND (data->>'mapLongitude')::float != 0
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
