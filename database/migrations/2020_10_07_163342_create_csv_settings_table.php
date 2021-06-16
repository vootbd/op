<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsvSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['product', 'category','remote_island'])->default('product');
            $table->string('column_name', 100);
            $table->string('column_label',200);
            $table->text('column_description');
            $table->boolean('in_output')->default(1);
            $table->boolean('is_active')->default(1);
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->softDeletes();
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
        Schema::dropIfExists('csv_settings');
    }
}
