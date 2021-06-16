<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsvSettingsEcmallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_settings_ecmalls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['product', 'category','remote_island'])->default('product');
            $table->string('column_name', 100);
            $table->string('column_label',200);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csv_settings_ecmalls');
    }
}
