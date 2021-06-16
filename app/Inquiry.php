<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name',
        'inquiry_items',
        'email',
        'inquiry_content',
        'created_by',
        'updated_by'
    ];
}
