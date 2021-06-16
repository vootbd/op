<?php

use Illuminate\Database\Seeder;
use App\Island;

class IslandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $islands = [
            ['name' => 'island 1', 'code' => '23451', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 2', 'code' => '23411', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 3', 'code' => '23421', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 4', 'code' => '23431', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 5', 'code' => '23441', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 6', 'code' => '23b51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 7', 'code' => '23461', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 8', 'code' => '23471', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 9', 'code' => '23481', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 10', 'code' => '23951', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 11', 'code' => '23101', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 12', 'code' => '23a51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 13', 'code' => '2d4s1', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 15', 'code' => '2g451', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 16', 'code' => '23q51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 17', 'code' => '23e51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 18', 'code' => '23t51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 19', 'code' => '23y51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 20', 'code' => '23u51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 21', 'code' => '23i51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 22', 'code' => '23o51', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'island 23', 'code' => '23p51', 'created_by' => 1, 'updated_by' => 1]
         ];
         foreach ($islands as $island) {
              Island::create($island);
         }
    }
}
