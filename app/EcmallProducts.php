<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EcmallProducts extends Model
{
    protected $fillable = [
        'ecmall_product_url',
        'ecmall_product_name',
        'base_image',
        'small_image',
        'thumbnail_image',
        'ecmall_product_description',
        'ecmall_short_description',
        'ecmall_shipping_weight',
        'ecmall_selling_price',
        'ecmall_quantity_update_status',
        'ecmall_stock_quantity',
        'ecmall_seller_id',
        'ecmall_temperature',
        'ecmall_sku',
        'product_id',
        'created_by',
        'updated_by'
    ];
    public function ecmall_sku(){

        return $this->belongsTo('App\Product', 'ecmall_sku');
    }
    public function ecmallProductImages()
    {
        return $this->hasMany('App\EcmallProductImage');
    }
    
}
