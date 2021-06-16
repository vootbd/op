<?php

namespace App;
use App\Area;
use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $softDelete = true;

    protected $fillable = [
        'area_id','name','url_map','created_by'
    ];

    public function area(){
        return $this->belongsTo(Area::class);
    }

    public function islands(){
        return $this->hasMany(Island::class);
    }
}
