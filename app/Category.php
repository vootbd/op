<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'parent_id','name','order', 'created_by','updated_by'
    ];

    public function children()
    {
        return $this->hasMany(Category::class,'parent_id','id')->orderBy('order');
    }

    public function parent(){
        return $this->belongsTo(Category::class,'parent_id','id');
    }
}
