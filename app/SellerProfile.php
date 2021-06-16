<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'profile1',
        'profile2',
        'profile3',
        'profile4',
        'profile5',
        'cover_image',
        'cover_image_md',
        'cover_image_sm',
        'created_by',
        'updated_by'
    ];
}
