<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['name' => 'category 1', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 2', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 3', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 4', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 5', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 6', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 7', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 8', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 9', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 10', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 11', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 12', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 13', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 15', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 16', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 17', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 18', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 19', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 20', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 21', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 22', 'created_by' => 1, 'updated_by' => 1],
            ['name' => 'category 23', 'created_by' => 1, 'updated_by' => 1]
         ];
         foreach ($datas as $data) {
            Category::create($data);
         }
    }
}
