<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerContact extends Model
{
    protected $fillable = [
        'user_id',
        'number_of_employe',
        'representative',
        'high_sales',
        'telephone',
        'fax',
        'contact_email',
        'contact_name',
        'url',
        'created_by',
        'updated_by'
    ];
}
