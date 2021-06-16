<?php

use Illuminate\Database\Seeder;
use App\AllergyIndications;

class AllergyIndicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['name' => 'えび', 'is_recommended' => 0, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'かに', 'is_recommended' => 0, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '小麦', 'is_recommended' => 0, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'そば', 'is_recommended' => 0, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '卵', 'is_recommended' => 0, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '乳', 'is_recommended' => 0, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '落花生', 'is_recommended' => 0, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'あわび', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'いか', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'いくら', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'オレンジ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'カシューナッツ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'キウイフルーツ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '牛肉', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'くるみ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'ごま ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'さけ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'さば ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '大豆', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '鶏肉', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'バナナ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => '豚肉', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'まつたけ', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'もも', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'やまいも', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'りんご', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'ゼラチン', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'アーモンド', 'is_recommended' => 1, 'created_by' => 1, 'updated_by' => 1]
         ];
         foreach ($datas as $data) {
            AllergyIndications::create($data);
         }
    }
}
