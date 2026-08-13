<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationRequestNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('location_request_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_request_id');
            $table->unsignedBigInteger('user_id');
            $table->string('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_request_notifications');
    }
}
