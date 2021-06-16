<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivity extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'action','email','description','log_level','ip','browser'
    ];
}
