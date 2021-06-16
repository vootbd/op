<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\FullTextSearch;

class Product extends Model
{
    use SoftDeletes, FullTextSearch;
    protected $fillable = [
        'ecmall_link',
        'ecmall_sku',
        'island_id',
        'seller_id',
        'status',
        'name',
        'product_explanation',
        'category_id',
        'price',
        'tax',
        'sell_price',
        'telephone',
        'fax',
        'email',
        'contact_name',
        'cover_image',
        'cover_image_md',
        'cover_image_sm',
        'url',
        'shipment_method',
        'preservation_method',
        'package_type',
        'quality_retention_temperature',
        'expiration_taste_quality',
        'use_scene',
        'created_by',
        'updated_by',
        'additional_image'
    ];
    
    protected $searchable = [
        'name'
    ];

    public function salesDestination(){
        return $this->belongsToMany(SalesDestination::class);
    }

    //Prdouct Iamges table  relation for product id
    public function productImages()
    {
        return $this->hasMany('App\ProductImage');
    }

    public function productAdditionalInformations()
    {
        return $this->hasMany('App\AdditionalInformations');
    }

    public function productAllergyIndication()
    {
        return $this->hasMany('App\ProductAllergyIndication');
    }

    public function productSalesDestination()
    {
        return $this->hasMany('App\ProductSalesDestination');
    }

    public function productSeller()
    {
        return $this->hasMany('App\User','id','seller_id');
    }

    public function localVendorSeller()
    {
        return $this->hasMany('App\LocalvendorSeller','seller_id','seller_id');
    }
    
    public function ecmallProductImages()
    {
        return $this->hasMany('App\EcmallProductImage');
    }

    public function ecmallSkuCrossell()
    {
        return $this->hasMany('App\SkuCrosssell');
    }
}
