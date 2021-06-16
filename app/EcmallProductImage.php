<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EcmallProductImage extends Model
{
    protected $fillable = [
        'image',
        'image_sm', 
        'image_md',
        'product_id',
        'created_by',
        'updated_by',
        'image_serial', 
    ];

    public function ecmall_product()
    {
        return $this->belongsTo('App\EcmallProducts');
    }
    public function rito_product()
    {
        return $this->belongsTo('App\Product');
    }
}
