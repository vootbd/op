<?php

use Illuminate\Database\Seeder;
use App\Product;
use App\AdditionalInformations;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'product 1 年8月に発売されたオンライ',
                'product_explanation' => 'Mediumは、Evan Williamsによって開発され、2012年8月に発売されたオンライン出版プラットフォームです。AMediumが所有しています。',
                'island_id' => 15,
                'seller_id' => 3,
                'category_id' => 5,
                'price' => 2300,
                'tax' => 8,
                'sell_price' => 2500,
                'cover_image' => '/upload/product/cover_image/20200214125131.jpg',
                'cover_image_sm' => '/upload/product/cover_image/sm/20200214125131_sm=116x132.jpg',
                'cover_image_md' => '/upload/product/cover_image/md/20200214125131_md=294x350.jpg',
                'created_at' => '2015-01-04 15:22:28',
                'created_by' => 2,
                'updated_by' => 2
            ],
            [
                'name' => 'product 2 年8月に発売されたオンライ',
                'product_explanation' => 'Mediumは、Evan Williamsによって開発され、2012年8月に発売されたオンライン出版プラットフォームです。AMediumが所有しています。',
                'island_id' => 5,
                'seller_id' => 12,
                'category_id' => 5,
                'price' => 300,
                'tax' => 8,
                'sell_price' => 500,
                'cover_image' => '/upload/product/cover_image/20200214125131.jpg',
                'cover_image_sm' => '/upload/product/cover_image/sm/20200214125131_sm=116x132.jpg',
                'cover_image_md' => '/upload/product/cover_image/md/20200214125131_md=294x350.jpg',
                'created_by' => 2,
                'updated_by' => 2
            ],
            [
                'name' => '年8月に発売されたオンライ 年8月に発売されたオンライ',
                'product_explanation' => 'Mediumは、Evan Williamsによって開発され、2012年8月に発売されたオンライン出版プラットフォームです。AMediumが所有しています。',
                'island_id' => 5,
                'seller_id' => 12,
                'category_id' => 4,
                'price' => 3330,
                'tax' => 10,
                'sell_price' => 5330,
                'cover_image' => '/upload/product/cover_image/20200214125131.jpg',
                'cover_image_sm' => '/upload/product/cover_image/sm/20200214125131_sm=116x132.jpg',
                'cover_image_md' => '/upload/product/cover_image/md/20200214125131_md=294x350.jpg',
                'created_by' => 2,
                'updated_by' => 2
            ]
         ];
         foreach ($products as $product) {
              Product::create($product);
         }

         factory(App\Product::class, 10000)->create();

        //  Sales destination
        $datas = [
            ['product_id' => 1, 'sales_destination_id' => 1],
            ['product_id' => 1, 'sales_destination_id' => 3],
            ['product_id' => 1, 'sales_destination_id' => 5],
            ['product_id' => 1, 'sales_destination_id' => 2],
            ['product_id' => 2, 'sales_destination_id' => 3],
            ['product_id' => 2, 'sales_destination_id' => 5],
            ['product_id' => 3, 'sales_destination_id' => 1],
            ['product_id' => 3, 'sales_destination_id' => 2]
         ];
         foreach ($datas as $data) {
            DB::table('product_sales_destination')->insert($data);
         }

        //  Additional Informations
        $additionals = [
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 1, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 2, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa'],
            ['product_id' => 3, 'description' => 'aaaa']
         ];
         foreach ($additionals as $additional) {
            AdditionalInformations::create($additional);
         }
    }
}
