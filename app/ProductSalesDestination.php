<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSalesDestination extends Model
{
    protected $table = 'product_sales_destination';
    protected $fillable = [
        'product_id',
        'sales_destination_id'
    ];
}
