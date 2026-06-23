<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration is irreversible — the JSONB `data` columns on
     * `restaurants` and `hours` are dropped permanently. All code that
     * references `$model->data[...]` or `data->>` must be refactored
     * to use the new relational columns before running this migration.
     */
    public function up(): void
    {
        DB::statement('DROP INDEX IF EXISTS restaurants_active_location_cursor_idx');
        DB::statement('DROP INDEX IF EXISTS restaurants_poi_id_unique_index');
        DB::statement('DROP INDEX IF EXISTS restaurants_data_index');
        DB::statement('DROP INDEX IF EXISTS hours_data_index');

        Schema::dropColumns('restaurants', ['data']);
        Schema::dropColumns('hours', ['data']);

        $activeStatusId = (int) DB::scalar(<<<'SQL'
            SELECT id FROM statuses WHERE code = 10 AND text IS NULL LIMIT 1
        SQL);

        DB::unprepared(<<<SQL
            CREATE INDEX restaurants_active_cursor_idx
            ON restaurants (id)
            WHERE status_id = {$activeStatusId}
              AND status_text IS NULL
        SQL);
    }

    /**
     * Reverse the migrations.
     *
     * The `data` columns cannot be repopulated after being dropped.
     * Re-adding them as empty nullable columns restores the schema
     * structure, but the JSON data is permanently lost.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS restaurants_active_cursor_idx');

        Schema::table('restaurants', function ($table) {
            $table->jsonb('data')->nullable();
        });

        Schema::table('hours', function ($table) {
            $table->jsonb('data')->nullable();
        });
    }
};
