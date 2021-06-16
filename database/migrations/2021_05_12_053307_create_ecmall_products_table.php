<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcmallProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecmall_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('ecmall_sku', 20);
            $table->text('base_image');
            $table->text('small_image')->nullable()->default(NULL);
            $table->text('thumbnail_image')->nullable()->default(NULL);
            $table->string('ecmall_product_url', 100)->nullable();
            $table->string('ecmall_product_name', 100)->nullable();
            $table->text('ecmall_product_description')->nullable();
            $table->text('ecmall_short_description')->nullable();
            $table->float('ecmall_shipping_weight',10, 2)->nullable()->default(0.00);
            $table->bigInteger('ecmall_selling_price')->nullable()->default(0);
            $table->tinyInteger('ecmall_quantity_update_status')->default(0);
            $table->bigInteger('ecmall_stock_quantity')->nullable()->default(0);
            $table->integer('ecmall_seller_id')->nullable();
            $table->enum('ecmall_temperature', array('Ambient', 'Cool', 'Frozen'));
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecmall_products');
    }
}
