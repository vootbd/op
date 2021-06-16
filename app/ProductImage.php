<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    // protected $table = "product_images";
    protected $fillable = [
        'product_id',
        'image',
        'image_sm',
        'image_md',
        'image_serial'
    ];

    //product table relation 
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
