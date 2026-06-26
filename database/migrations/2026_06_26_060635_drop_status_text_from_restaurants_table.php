<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The ``status_text`` column on ``restaurants`` duplicated the ``text``
     * field already stored on the related ``statuses`` row. The active global
     * scope now relies solely on ``status_id`` matching the active status,
     * since a non-active restaurant already carries a different ``status_id``.
     */
    public function up(): void
    {
        DB::statement('DROP INDEX IF EXISTS restaurants_active_cursor_idx');

        Schema::dropColumns('restaurants', ['status_text']);

        $activeStatusId = (int) DB::scalar(<<<'SQL'
            SELECT id FROM statuses WHERE code = 10 AND text IS NULL LIMIT 1
        SQL);

        DB::unprepared(<<<SQL
            CREATE INDEX restaurants_active_cursor_idx
            ON restaurants (id)
            WHERE status_id = {$activeStatusId}
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS restaurants_active_cursor_idx');

        Schema::table('restaurants', function ($table) {
            $table->string('status_text')->nullable();
        });

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
};
