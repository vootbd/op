<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $fillable=[
        'token','counter','is_blocked','created_at','updated_at'
    ];
}
