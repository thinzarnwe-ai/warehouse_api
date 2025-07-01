<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTrackingRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_tracking_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('stock_tracking_id');
            $table->string('status');
            $table->integer('qty');
            $table->integer('user_id');
            $table->text('remark');
            $table->string('transfer_location')->nullable();
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
        Schema::dropIfExists('stock_tracking_records');
    }
}
