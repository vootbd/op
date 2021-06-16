<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkuCrosssell extends Model
{
    protected $table = 'sku_crosssell';
    protected $fillable = [ 
        'product_id',
        'created_by',
        'updated_by', 
        'cart_sku'
    ];
    public function cart_sku(){

        return $this->belongsTo('App\Product', 'ecmall_sku');
    }

    public function crossed_sku(){

        return $this->belongsTo('App\Product', 'ecmall_sku');
    }
}
