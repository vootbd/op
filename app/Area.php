<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class Area extends Model
{
    public static function  areaList(){
        $areas = Area::orderBy('order_index', 'asc')->where('is_active',1)->select('id','area_name','is_active')->get();
        return $areas;
    }
    public function prefectures(){
        return $this->hasMany(Prefecture::class);
    }
}