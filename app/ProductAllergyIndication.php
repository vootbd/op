<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAllergyIndication extends Model
{
    protected $table = "product_allergy_indication";
    protected $fillable = [
        'product_id',
        'allergy_indication_id'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
