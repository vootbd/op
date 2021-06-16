<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence($nbWords = 1, $variableNbWords = true),
        'product_explanation' => $faker->sentence($nbWords = 20, $variableNbWords = true),
        'island_id' => $faker->numberBetween($min = 1, $max = 15),
        'seller_id' => $faker->numberBetween($min = 1, $max = 5),
        'category_id' => $faker->numberBetween($min = 1, $max = 15),
        'price' => $faker->numberBetween($min = 1, $max = 1500),
        'tax' => 10,
        'sell_price' => $faker->numberBetween($min = 1, $max = 1500),
        'cover_image' => '/upload/product/cover_image/20200214125131.jpg',
        'cover_image_sm' => '/upload/product/cover_image/sm/20200214125131_sm=116x132.jpg',
        'cover_image_md' => '/upload/product/cover_image/md/20200214125131_md=294x350.jpg',
        'created_by' => 2,
        'updated_by' => 2
    ];
});
