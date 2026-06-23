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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->integer('poi_id')->nullable();
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->string('status_text')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
        });

        DB::statement(<<<'SQL'
            WITH extracted AS (
                SELECT
                    r.id,
                    (r.data->>'poiId')::int AS poi_id,
                    r.data->>'name' AS name,
                    NULLIF(r.data->>'shortenUrl', '') AS url,
                    s.id AS status_id,
                    NULLIF(r.data->>'statusText', '') AS status_text,
                    NULLIF(r.data->>'address', '') AS address,
                    d.id AS district_id
                FROM restaurants r
                LEFT JOIN statuses s
                    ON s.code = (r.data->>'status')::smallint
                   AND s.text IS NOT DISTINCT FROM NULLIF(r.data->>'statusText', '')
                LEFT JOIN districts d
                    ON d.external_id = (r.data->'district'->>'districtId')::int
            )
            UPDATE restaurants
            SET
                poi_id = e.poi_id,
                name = e.name,
                url = e.url,
                status_id = e.status_id,
                status_text = e.status_text,
                address = e.address,
                district_id = e.district_id
            FROM extracted e
            WHERE restaurants.id = e.id
        SQL);

        Schema::table('restaurants', function (Blueprint $table) {
            $table->integer('poi_id')->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
            $table->unsignedBigInteger('status_id')->nullable(false)->change();

            $table->unique('poi_id');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('district_id')->references('id')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['district_id']);
            $table->dropUnique(['poi_id']);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'poi_id',
                'name',
                'url',
                'status_id',
                'status_text',
                'address',
                'district_id',
            ]);
        });
    }
};
