<?php

use Illuminate\Database\Seeder;
use App\Area;

class AreasTableSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $areas = [
            ['area_name' => '北海道', 'order_index' => '1','url_map' => 'hokkaido', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '東北', 'order_index' => '2','url_map' => 'tohoku', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '北陸・甲信越', 'order_index' => '3','url_map' => 'hokuriku-koushinetsu', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '中国', 'order_index' => '4','url_map' => 'chugoku', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '関東', 'order_index' => '5','url_map' => 'kanto', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '関西', 'order_index' => '6','url_map' => 'kansai', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '東海', 'order_index' => '7','url_map' => 'tokai', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '九州・沖縄', 'order_index' => '8','url_map' => 'kyushu-okinawa', 'created_by' => '1', 'is_active' => '1'],
            ['area_name' => '四国', 'order_index' => '9','url_map' => 'shikoku', 'created_by' => '1', 'is_active' => '1'],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}