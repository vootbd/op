<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url_map', 100);
            $table->string('page_title', 190);
            $table->mediumText('description');
            $table->text('search_keys', 2000)->nullable()->default(NULL);
            $table->dateTime('publishing_date')->nullable()->default(NULL);
            $table->dateTime('publishing_end_date')->nullable()->default(NULL);
            $table->unsignedBigInteger('directory_id');
            $table->foreign('directory_id')->references('id')->on('directories');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->boolean('status_label')
                ->default(2)
                ->comment = '1=公開終了, 2=公開日, 3=更新日';
            $table->boolean('is_active')->default(1);
            $table->text('page_css')->nullable();
            $table->dateTime('display_date');

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
        Schema::dropIfExists('pages');
    }
}
