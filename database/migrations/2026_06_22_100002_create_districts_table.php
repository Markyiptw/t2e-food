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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->integer('external_id');
            $table->string('name');
            $table->timestamps();
        });

        DB::statement(<<<'SQL'
            CREATE UNIQUE INDEX districts_external_id_unique
            ON districts (external_id)
        SQL);

        DB::statement(<<<'SQL'
            INSERT INTO districts (external_id, name, created_at, updated_at)
            SELECT DISTINCT
                (data->'district'->>'districtId')::int,
                data->'district'->>'name',
                NOW(),
                NOW()
            FROM restaurants
            WHERE data->'district' IS NOT NULL
              AND data->'district'->>'districtId' IS NOT NULL
            ORDER BY 1
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
