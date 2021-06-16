<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkuRelation extends Model
{
    public function main_sku(){

        return $this->belongsTo('App\Product', 'ecmall_sku');
    }

    public function related_sku(){

        return $this->belongsTo('App\Product', 'ecmall_sku');
    }
}
