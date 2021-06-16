<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalvendorEcmallid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localvendor_ecmallid', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('localvendor_id')->onDelete('cascade');
            $table->foreign('localvendor_id')->references('id')->on('users');
            $table->integer('ecmall_seller_id');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('localvendor_ecmallid');
    }
}
