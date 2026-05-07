<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE INDEX periods_start_index
            ON periods (start)
        SQL);

        DB::statement(<<<'SQL'
            CREATE INDEX periods_end_index
            ON periods ("end")
        SQL);

        DB::statement(<<<'SQL'
            CREATE INDEX hours_data_index
            ON hours USING GIN (data)
        SQL);
    }

    public function down(): void
    {
        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS periods_start_index
        SQL);

        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS periods_end_index
        SQL);

        DB::statement(<<<'SQL'
            DROP INDEX IF EXISTS hours_data_index
        SQL);
    }
};
