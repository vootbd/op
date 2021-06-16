<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTableSeeder::class);
        $this->call(CreateAdminUserSeeder::class);
        $this->call(IslandTableSeeder::class);
        $this->call(SalerDestinationsTableSeeder::class);
        $this->call(AllergyIndicationsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        //$this->call(AreasTableSeeder::class);
        //$this->call(PrefecturesTableSeeder::class);
        // $this->call(ProductsTableSeeder::class);
    }
}
