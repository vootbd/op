<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalInformations extends Model
{
    protected $fillable = [
        'product_id',
        'description'
    ];
}
