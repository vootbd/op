<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'seller_id',
        'comment',
        'created_by',
        'updated_by'
    ];
}
