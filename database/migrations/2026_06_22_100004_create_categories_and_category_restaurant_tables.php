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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('category_restaurant', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['category_id', 'restaurant_id']);
        });

        DB::statement(<<<'SQL'
            INSERT INTO categories (name, created_at, updated_at)
            SELECT DISTINCT category->>'name', NOW(), NOW()
            FROM restaurants
            CROSS JOIN jsonb_array_elements(data->'categories') AS category
            WHERE data->'categories' IS NOT NULL
              AND category->>'name' IS NOT NULL
            ORDER BY 1
        SQL);

        DB::statement(<<<'SQL'
            INSERT INTO category_restaurant (category_id, restaurant_id, created_at, updated_at)
            SELECT DISTINCT
                c.id,
                r.id,
                NOW(),
                NOW()
            FROM restaurants r
            CROSS JOIN jsonb_array_elements(r.data->'categories') AS category
            JOIN categories c ON c.name = category->>'name'
            WHERE r.data->'categories' IS NOT NULL
              AND category->>'name' IS NOT NULL
            ON CONFLICT DO NOTHING
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_restaurant');
        Schema::dropIfExists('categories');
    }
};
