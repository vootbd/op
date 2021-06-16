<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Prefecture;

class Island extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'jurisdiction',
        'autonomous_code',
        'prefecture_id',
        'created_by',
        'updated_by'
    ];

    public function prefectures(){
        return $this->belongsTo(Prefecture::class, 'prefecture_id', 'id');
    }
}
