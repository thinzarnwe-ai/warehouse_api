<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixLocationsIdSequence extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(
            "SELECT setval(
                pg_get_serial_sequence('locations', 'id'),
                COALESCE((SELECT MAX(id) FROM locations), 1),
                true
            )"
        );
    }

    public function down()
    {
        // No rollback needed for sequence correction.
    }
}
