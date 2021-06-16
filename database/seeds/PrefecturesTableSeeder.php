<?php

use Illuminate\Database\Seeder;
use App\Prefecture;

class PrefecturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prefectures = [
            ['url_map' => 'hokkaido', 'name' => '北海道', 'area_id' => 1, 'created_by' => 1],

            ['url_map' => 'aomori', 'name' => '青森', 'area_id' => 2, 'created_by' => 1],
            ['url_map' => 'iwate', 'name' => '岩手', 'area_id' => 2, 'created_by' => 1],
            ['url_map' => 'miyagi ', 'name' => '宮城', 'area_id' => 2, 'created_by' => 1],
            ['url_map' => 'akita', 'name' => '秋田', 'area_id' => 2, 'created_by' => 1],
            ['url_map' => 'yamagata', 'name' => '山形', 'area_id' => 2, 'created_by' => 1],
            ['url_map' => 'fukushima', 'name' => '福島', 'area_id' => 2, 'created_by' => 1],

            ['url_map' => 'ibaraki', 'name' => '茨城', 'area_id' => 5, 'created_by' => 1],
            ['url_map' => 'tochigi', 'name' => '栃木', 'area_id' => 5, 'created_by' => 1],
            ['url_map' => 'gunma ', 'name' => '群馬', 'area_id' => 5, 'created_by' => 1],
            ['url_map' => 'saitama', 'name' => '埼玉', 'area_id' => 5, 'created_by' => 1],
            ['url_map' => 'chiba', 'name' => '千葉', 'area_id' => 5, 'created_by' => 1],
            ['url_map' => 'tokyo', 'name' => '東京', 'area_id' => 5, 'created_by' => 1],
            ['url_map' => 'kanagawa', 'name' => '神奈川', 'area_id' => 5, 'created_by' => 1],

            ['url_map' => 'niigata', 'name' => '新潟', 'area_id' => 3, 'created_by' => 1],
            ['url_map' => 'toyama', 'name' => '富山', 'area_id' => 3, 'created_by' => 1],
            ['url_map' => 'ishikawa ', 'name' => '石川', 'area_id' => 3, 'created_by' => 1],
            ['url_map' => 'fukui', 'name' => '福井', 'area_id' => 3, 'created_by' => 1],
            ['url_map' => 'yamanashi', 'name' => '山梨', 'area_id' => 3, 'created_by' => 1],
            ['url_map' => 'nagano', 'name' => '長野', 'area_id' => 3, 'created_by' => 1],

            ['url_map' => 'gifu', 'name' => '岐阜', 'area_id' => 7, 'created_by' => 1],
            ['url_map' => 'shizuoka', 'name' => '静岡', 'area_id' => 7, 'created_by' => 1],
            ['url_map' => 'aichi', 'name' => '愛知', 'area_id' => 7, 'created_by' => 1],
            ['url_map' => 'mie', 'name' => '三重', 'area_id' => 7, 'created_by' => 1],

            ['url_map' => 'shiga', 'name' => '滋賀', 'area_id' => 6, 'created_by' => 1],
            ['url_map' => 'kyoto', 'name' => '京都', 'area_id' => 6, 'created_by' => 1],
            ['url_map' => 'osaka', 'name' => '大阪', 'area_id' => 6, 'created_by' => 1],
            ['url_map' => 'hyogo', 'name' => '兵庫', 'area_id' => 6, 'created_by' => 1],
            ['url_map' => 'nara', 'name' => '奈良', 'area_id' => 6, 'created_by' => 1],
            ['url_map' => 'wakayama', 'name' => '和歌山', 'area_id' => 6, 'created_by' => 1],

            ['url_map' => 'tottori', 'name' => '鳥取', 'area_id' => 4, 'created_by' => 1],
            ['url_map' => 'shimane', 'name' => '島根', 'area_id' => 4, 'created_by' => 1],
            ['url_map' => 'okayama', 'name' => '岡山', 'area_id' => 4, 'created_by' => 1],
            ['url_map' => 'hiroshima', 'name' => '広島', 'area_id' => 4, 'created_by' => 1],
            ['url_map' => 'yamaguchi', 'name' => '山口', 'area_id' => 4, 'created_by' => 1],

            ['url_map' => 'tokushima', 'name' => '徳島', 'area_id' => 9, 'created_by' => 1],
            ['url_map' => 'kagawa', 'name' => '香川', 'area_id' => 9, 'created_by' => 1],
            ['url_map' => 'ehime', 'name' => '愛媛', 'area_id' => 9, 'created_by' => 1],
            ['url_map' => 'kochi', 'name' => '高知', 'area_id' => 9, 'created_by' => 1],

            ['url_map' => 'fukuoka', 'name' => '福岡', 'area_id' => 8, 'created_by' => 1],
            ['url_map' => 'saga', 'name' => '佐賀', 'area_id' => 8, 'created_by' => 1],
            ['url_map' => 'nagasaki', 'name' => '長崎', 'area_id' => 8, 'created_by' => 1],
            ['url_map' => 'kumamoto', 'name' => '熊本', 'area_id' => 8, 'created_by' => 1],
            ['url_map' => 'oita', 'name' => '大分', 'area_id' => 8, 'created_by' => 1],
            ['url_map' => 'miyazaki', 'name' => '宮崎', 'area_id' => 8, 'created_by' => 1],
            ['url_map' => 'kagoshima', 'name' => '鹿児島', 'area_id' => 8, 'created_by' => 1],
            ['url_map' => 'okinawa', 'name' => '沖縄', 'area_id' => 8, 'created_by' => 1],
            ];

        foreach ($prefectures as $prefecture) {
            Prefecture::create($prefecture);
        }
    }
}
