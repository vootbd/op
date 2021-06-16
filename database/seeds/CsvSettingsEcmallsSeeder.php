<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CsvSettingsEcmallsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvColumnsEcMalls = [
            ['type' => 'product', 'column_name' => 'sku', 'column_label' => 'sku'],
            ['type' => 'product', 'column_name' => 'store_view_code', 'column_label' => 'store_view_code'],
            ['type' => 'product', 'column_name' => 'attribute_set_code', 'column_label' => 'attribute_set_code'],
            ['type' => 'product', 'column_name' => 'product_type', 'column_label' => 'product_type'],
            ['type' => 'product', 'column_name' => 'categories', 'column_label' => 'categories'],
            ['type' => 'product', 'column_name' => 'product_websites', 'column_label' => 'product_websites'],
            ['type' => 'product', 'column_name' => 'name', 'column_label' => 'name'],
            ['type' => 'product', 'column_name' => 'description', 'column_label' => 'description'],
            ['type' => 'product', 'column_name' => 'short_description', 'column_label' => 'short_description'],
            ['type' => 'product', 'column_name' => 'weight', 'column_label' => 'weight'],
            ['type' => 'product', 'column_name' => 'product_online', 'column_label' => 'product_online'],
            ['type' => 'product', 'column_name' => 'tax_class_name', 'column_label' => 'tax_class_name'],
            ['type' => 'product', 'column_name' => 'visibility', 'column_label' => 'visibility'],
            ['type' => 'product', 'column_name' => 'price', 'column_label' => 'price'],
            ['type' => 'product', 'column_name' => 'special_price', 'column_label' => 'special_price'],
            ['type' => 'product', 'column_name' => 'special_price_from_date', 'column_label' => 'special_price_from_date'],
            ['type' => 'product', 'column_name' => 'special_price_to_date', 'column_label' => 'special_price_to_date'],
            ['type' => 'product', 'column_name' => 'url_key', 'column_label' => 'url_key'],
            ['type' => 'product', 'column_name' => 'meta_title', 'column_label' => 'meta_title'],
            ['type' => 'product', 'column_name' => 'meta_keywords', 'column_label' => 'meta_keywords'],
            ['type' => 'product', 'column_name' => 'meta_description', 'column_label' => 'meta_description'],
            ['type' => 'product', 'column_name' => 'created_at', 'column_label' => 'created_at'],
            ['type' => 'product', 'column_name' => 'updated_at', 'column_label' => 'updated_at'],
            ['type' => 'product', 'column_name' => 'new_from_date', 'column_label' => 'new_from_date'],
            ['type' => 'product', 'column_name' => 'new_to_date', 'column_label' => 'new_to_date'],
            ['type' => 'product', 'column_name' => 'display_product_options_in', 'column_label' => 'display_product_options_in'],
            ['type' => 'product', 'column_name' => 'map_price', 'column_label' => 'map_price'],
            ['type' => 'product', 'column_name' => 'msrp_price', 'column_label' => 'msrp_price'],
            ['type' => 'product', 'column_name' => 'map_enabled', 'column_label' => 'map_enabled'],
            ['type' => 'product', 'column_name' => 'gift_message_available', 'column_label' => 'gift_message_available'],
            ['type' => 'product', 'column_name' => 'custom_design', 'column_label' => 'custom_design'],
            ['type' => 'product', 'column_name' => 'custom_design_from', 'column_label' => 'custom_design_from'],
            ['type' => 'product', 'column_name' => 'custom_design_to', 'column_label' => 'custom_design_to'],
            ['type' => 'product', 'column_name' => 'custom_layout_update', 'column_label' => 'custom_layout_update'],
            ['type' => 'product', 'column_name' => 'page_layout', 'column_label' => 'page_layout'],
            ['type' => 'product', 'column_name' => 'product_options_container', 'column_label' => 'product_options_container'],
            ['type' => 'product', 'column_name' => 'msrp_display_actual_price_type', 'column_label' => 'msrp_display_actual_price_type'],
            ['type' => 'product', 'column_name' => 'country_of_manufacture', 'column_label' => 'country_of_manufacture'],
            ['type' => 'product', 'column_name' => 'additional_attributes', 'column_label' => 'additional_attributes'],
            ['type' => 'product', 'column_name' => 'qty', 'column_label' => 'qty'],
            ['type' => 'product', 'column_name' => 'out_of_stock_qty', 'column_label' => 'out_of_stock_qty'],
            ['type' => 'product', 'column_name' => 'use_config_min_qty', 'column_label' => 'use_config_min_qty'],
            ['type' => 'product', 'column_name' => 'is_qty_decimal', 'column_label' => 'is_qty_decimal'],
            ['type' => 'product', 'column_name' => 'allow_backorders', 'column_label' => 'allow_backorders'],
            ['type' => 'product', 'column_name' => 'use_config_backorders', 'column_label' => 'use_config_backorders'],
            ['type' => 'product', 'column_name' => 'min_cart_qty', 'column_label' => 'min_cart_qty'],
            ['type' => 'product', 'column_name' => 'use_config_min_sale_qty', 'column_label' => 'use_config_min_sale_qty'],
            ['type' => 'product', 'column_name' => 'max_cart_qty', 'column_label' => 'max_cart_qty'],
            ['type' => 'product', 'column_name' => 'use_config_max_sale_qty', 'column_label' => 'use_config_max_sale_qty'],
            ['type' => 'product', 'column_name' => 'is_in_stock', 'column_label' => 'is_in_stock'],
            ['type' => 'product', 'column_name' => 'notify_on_stock_below', 'column_label' => 'notify_on_stock_below'],
            ['type' => 'product', 'column_name' => 'use_config_notify_stock_qty', 'column_label' => 'use_config_notify_stock_qty'],
            ['type' => 'product', 'column_name' => 'manage_stock', 'column_label' => 'manage_stock'],
            ['type' => 'product', 'column_name' => 'use_config_manage_stock', 'column_label' => 'use_config_manage_stock'],
            ['type' => 'product', 'column_name' => 'use_config_qty_increments', 'column_label' => 'use_config_qty_increments'],
            ['type' => 'product', 'column_name' => 'qty_increments', 'column_label' => 'qty_increments'],
            ['type' => 'product', 'column_name' => 'use_config_enable_qty_inc', 'column_label' => 'use_config_enable_qty_inc'],
            ['type' => 'product', 'column_name' => 'enable_qty_increments', 'column_label' => 'enable_qty_increments'],
            ['type' => 'product', 'column_name' => 'is_decimal_divided', 'column_label' => 'is_decimal_divided'],
            ['type' => 'product', 'column_name' => 'website_id', 'column_label' => 'website_id'],
            ['type' => 'product', 'column_name' => 'deferred_stock_update', 'column_label' => 'deferred_stock_update'],
            ['type' => 'product', 'column_name' => 'use_config_deferred_stock_update', 'column_label' => 'use_config_deferred_stock_update'],
            ['type' => 'product', 'column_name' => 'related_skus', 'column_label' => 'related_skus'],
            ['type' => 'product', 'column_name' => 'crosssell_skus', 'column_label' => 'crosssell_skus'],
            ['type' => 'product', 'column_name' => 'upsell_skus', 'column_label' => 'upsell_skus'],
            ['type' => 'product', 'column_name' => 'hide_from_product_page', 'column_label' => 'hide_from_product_page'],
            ['type' => 'product', 'column_name' => 'custom_options', 'column_label' => 'custom_options'],
            ['type' => 'product', 'column_name' => 'bundle_price_type', 'column_label' => 'bundle_price_type'],
            ['type' => 'product', 'column_name' => 'bundle_sku_type', 'column_label' => 'bundle_sku_type'],
            ['type' => 'product', 'column_name' => 'bundle_price_view', 'column_label' => 'bundle_price_view'],
            ['type' => 'product', 'column_name' => 'bundle_weight_type', 'column_label' => 'bundle_weight_type'],
            ['type' => 'product', 'column_name' => 'bundle_values', 'column_label' => 'bundle_values'],
            ['type' => 'product', 'column_name' => 'associated_skus', 'column_label' => 'associated_skus'],
            ['type' => 'product', 'column_name' => 'seller_id', 'column_label' => 'seller_id'],
            ['type' => 'product', 'column_name' => 'base_image', 'column_label' => 'base_image'],
            ['type' => 'product', 'column_name' => 'small_image', 'column_label' => 'small_image'],
            ['type' => 'product', 'column_name' => 'thumbnail_image', 'column_label' => 'thumbnail_image'],
            ['type' => 'product', 'column_name' => 'additional_images', 'column_label' => 'additional_images'],
            ['type' => 'product', 'column_name' => 'custom_temperature', 'column_label' => 'custom_temperature'],
            ];

        foreach ($csvColumnsEcMalls as $csvColumnsEcMall) {
            DB::connection('mysql')
                ->table('csv_settings_ecmalls')
                ->insert($csvColumnsEcMall);
        }
    }
}
