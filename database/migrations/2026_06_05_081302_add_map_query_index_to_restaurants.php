<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE INDEX restaurants_map_idx
            ON restaurants (
                id,
                ((data->>'status')::int),
                (data->>'statusText'),
                (data->>'mapLatitude'),
                (data->>'mapLongitude')
            )
        SQL);
    }

    public function down(): void
    {
        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS restaurants_map_idx
        SQL);
    }
};
