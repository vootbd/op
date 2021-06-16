<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Directory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'parent_id', 'order', 'created_by'
    ];

    public function children()
    {
        return $this->hasMany(Directory::class, 'parent_id', 'id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(Directory::class, 'parent_id', 'id');
    }

    public function pages()
    {
        return $this->hasMany(Page::class, 'directory_id', 'id');
    }
}
