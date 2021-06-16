<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefectureIdToIslandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('islands', function (Blueprint $table) {
            $table->unsignedBigInteger('prefecture_id')->nullable()->after('code');
            $table->foreign('prefecture_id')->references('id')->on('prefectures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('islands', function (Blueprint $table) {
            //
        });
    }
}
