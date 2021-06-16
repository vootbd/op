<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;
    protected $table = 'medias';
    protected $fillable = [
        'display_name',
        'url',
        'original_name',
        'mime_type',
        'extention',
        'size',
        'width',
        'height',
        'alt_text',
        'created_by'
    ];
}
