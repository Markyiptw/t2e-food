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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('code');
            $table->string('text')->nullable();
            $table->timestamps();
        });

        DB::statement(<<<'SQL'
            CREATE UNIQUE INDEX statuses_code_text_unique
            ON statuses (code, text) NULLS NOT DISTINCT
        SQL);

        DB::statement(<<<'SQL'
            INSERT INTO statuses (code, text, created_at, updated_at)
            SELECT DISTINCT
                (data->>'status')::smallint,
                NULLIF(data->>'statusText', ''),
                NOW(),
                NOW()
            FROM restaurants
            ORDER BY 1, 2
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
