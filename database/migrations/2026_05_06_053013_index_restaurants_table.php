<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE INDEX restaurants_data_index
            ON restaurants USING GIN (data)
        SQL);

        DB::statement(<<<'SQL'
            CREATE UNIQUE INDEX restaurants_poi_id_unique_index
            ON restaurants ((data->>'poiId'))
        SQL);
    }
};
