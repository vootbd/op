<?php

use Illuminate\Database\Seeder;
use App\SalesDestination;

class SalerDestinationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['name' => '外食', 'created_by' => 1, 'updated_by' => 1],
            ['name' => '商社・卸売', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'メーカー', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'スーパーマーケット', 'created_by' => 1, 'updated_by' => 1],
            ['name' => '百貨店', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'ホテル ', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'その他', 'created_by' => 1, 'updated_by' => 1]
         ];
         foreach ($datas as $data) {
            SalesDestination::create($data);
         }
    }
}
