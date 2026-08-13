<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarkToLocationRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('location_requests', function (Blueprint $table) {
            $table->text('remark')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('location_requests', function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }
}
