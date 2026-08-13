<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('location_category');
            $table->string('location_type'); // S | W
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('row_id');
            $table->unsignedBigInteger('bay_id');
            $table->unsignedBigInteger('level_id');
            $table->string('side')->nullable(); // F, B, Natural
            $table->string('branch_short_name')->nullable();
            $table->string('location_name');
            $table->string('status')->default('request');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_requests');
    }
}
