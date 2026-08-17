<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationRequestDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('location_request_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id')->nullable(); // requester's active branch (for doc no)
            $table->string('branch_short_name')->nullable();
            $table->string('status')->default('request'); // request | completed | cancel
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        Schema::table('location_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable()->after('id');
            $table->index('document_id');
        });

        Schema::table('location_request_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable()->after('id');
            $table->index('document_id');
        });
    }

    public function down()
    {
        Schema::table('location_request_notifications', function (Blueprint $table) {
            $table->dropColumn('document_id');
        });

        Schema::table('location_requests', function (Blueprint $table) {
            $table->dropColumn('document_id');
        });

        Schema::dropIfExists('location_request_documents');
    }
}
