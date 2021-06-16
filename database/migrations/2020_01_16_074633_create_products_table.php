<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('island_id');
            $table->foreign('island_id')->references('id')->on('islands');
            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->boolean('status')->default(1);
            $table->string('name', 40);
            $table->text('product_explanation', 2000);
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->integer('price')->nullable();
            $table->tinyInteger('tax')->nullable();
            $table->integer('sell_price')->nullable();
            $table->string('cover_image', 255);
            $table->string('cover_image_sm', 255);
            $table->string('cover_image_md', 255);
            $table->string('url', 255)->nullable();
            $table->string('shipment_method', 100)->nullable();
            $table->string('preservation_method', 100)->nullable();
            $table->string('package_type', 100)->nullable();
            $table->string('quality_retention_temperature', 100)->nullable();
            $table->string('expiration_taste_quality', 2000)->nullable();
            $table->string('use_scene', 2000)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
        });
        
        DB::statement('ALTER TABLE db_products ADD FULLTEXT fulltext_index (name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
