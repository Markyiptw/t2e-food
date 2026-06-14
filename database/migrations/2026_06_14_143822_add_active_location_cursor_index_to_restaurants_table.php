<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS restaurants_map_idx
        SQL);

        DB::statement(<<<'SQL'
            CREATE INDEX restaurants_active_location_cursor_idx
            ON restaurants (id)
            WHERE
                ((data->>'status')::int = 10)
                AND (data->>'statusText') IS NULL
                AND (data->'mapLatitude') IS NOT NULL
                AND (data->'mapLongitude') IS NOT NULL
                AND ((data->>'mapLatitude')::float != 0)
                AND ((data->>'mapLongitude')::float != 0)
        SQL);
    }

    public function down(): void
    {
        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS restaurants_active_location_cursor_idx
        SQL);
    }
};
