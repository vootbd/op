<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFiledToIslandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('islands', function (Blueprint $table) {
            $table->string('jurisdiction', 255)->nullable()->after('code');
            $table->string('autonomous_code', 255)->nullable()->after('jurisdiction');
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
