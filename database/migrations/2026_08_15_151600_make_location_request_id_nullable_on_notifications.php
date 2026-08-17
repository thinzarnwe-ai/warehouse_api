<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeLocationRequestIdNullableOnNotifications extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE location_request_notifications ALTER COLUMN location_request_id DROP NOT NULL');
        } else {
            Schema::table('location_request_notifications', function ($table) {
                $table->unsignedBigInteger('location_request_id')->nullable()->change();
            });
        }
    }

    public function down()
    {
        // no-op
    }
}
