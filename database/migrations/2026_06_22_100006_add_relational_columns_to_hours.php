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
        DB::statement(<<<'SQL'
            DELETE FROM hours
            WHERE data->>'weight' IS NULL
               OR (data->>'weight')::int != 0
        SQL);

        Schema::table('hours', function (Blueprint $table) {
            $table->smallInteger('day_of_week')->nullable();
            $table->boolean('is_close')->nullable();
            $table->boolean('is_24hr')->nullable();
        });

        DB::statement(<<<'SQL'
            UPDATE hours SET
                day_of_week = COALESCE((data->>'dayOfWeek')::smallint, 0),
                is_close = COALESCE((data->>'isClose')::boolean, false),
                is_24hr = COALESCE((data->>'is24hr')::boolean, false)
        SQL);

        Schema::table('hours', function (Blueprint $table) {
            $table->smallInteger('day_of_week')->default(0)->nullable(false)->change();
            $table->boolean('is_close')->default(false)->nullable(false)->change();
            $table->boolean('is_24hr')->default(false)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hours', function (Blueprint $table) {
            $table->dropColumn(['day_of_week', 'is_close', 'is_24hr']);
        });
    }
};
