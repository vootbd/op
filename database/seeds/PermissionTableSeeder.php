<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * module manes are user, role, account-authority, activity-log, all-account-list, operator, buyer, seller, island, category, product, inquery, contact
     * @return void
     */
    public function run()
    {
        $permissions = [
           ['module' => 'role', 'name' => 'role-edit', 'display_name' => 'Role edit'],
           ['module' => 'activity-log', 'name' => 'activity-log', 'display_name' => 'Activity logs'],
           ['module' => 'account-unblock', 'name' => 'account-unblock', 'display_name' => 'Account Unblock'],
           ['module' => 'all-account', 'name' => 'all-account-list', 'display_name' => 'All account'],
           ['module' => 'all-account', 'name' => 'all-account-edit', 'display_name' => 'All account edit'],
           ['module' => 'all-account', 'name' => 'all-account-delete', 'display_name' => 'All account delete'],
           ['module' => 'operator-create', 'name' => 'operator-create', 'display_name' => 'Operators create'],
           ['module' => 'buyer', 'name' => 'buyer-list', 'display_name' => 'Buyers'],
           ['module' => 'buyer', 'name' => 'buyer-create', 'display_name' => 'Buyer create'],
           ['module' => 'buyer', 'name' => 'buyer-edit', 'display_name' => 'Buyer edit'],
           ['module' => 'buyer', 'name' => 'buyer-delete', 'display_name' => 'Buyer delete'],
           ['module' => 'seller', 'name' => 'seller-list', 'display_name' => 'Sellers'],
           ['module' => 'seller', 'name' => 'seller-create', 'display_name' => 'Seller create'],
           ['module' => 'seller', 'name' => 'seller-edit', 'display_name' => 'Seller edit'],
           ['module' => 'seller', 'name' => 'seller-delete', 'display_name' => 'Seller delete'],
           ['module' => 'island', 'name' => 'island-list', 'display_name' => 'Islands'],
           ['module' => 'island', 'name' => 'island-create', 'display_name' => 'Island create'],
           ['module' => 'island', 'name' => 'island-edit', 'display_name' => 'Island edit'],
           ['module' => 'island', 'name' => 'island-delete', 'display_name' => 'Island delete'],
           ['module' => 'category', 'name' => 'category-list', 'display_name' => 'Categories'],
           ['module' => 'category', 'name' => 'category-create', 'display_name' => 'Category create'],
           ['module' => 'category', 'name' => 'category-edit', 'display_name' => 'Category edit'],
           ['module' => 'category', 'name' => 'category-delete', 'display_name' => 'Category delete'],
           ['module' => 'product', 'name' => 'product-list', 'display_name' => 'Products'],
           ['module' => 'product', 'name' => 'product-create', 'display_name' => 'Product create'],
           ['module' => 'product', 'name' => 'product-edit', 'display_name' => 'Product edit'],
           ['module' => 'product', 'name' => 'product-delete', 'display_name' => 'Product delete'],
           ['module' => 'seller-buyer', 'name' => 'seller-buyer-list', 'display_name' => 'Buyers'],
           ['module' => 'seller-buyer', 'name' => 'seller-buyer-create', 'display_name' => 'Buyer create'],
           ['module' => 'seller-buyer', 'name' => 'seller-buyer-edit', 'display_name' => 'Buyer edit'],
           ['module' => 'seller-buyer', 'name' => 'seller-buyer-delete', 'display_name' => 'Buyer delete'],
           ['module' => 'seller-product', 'name' => 'seller-product-list', 'display_name' => 'Products'],
           ['module' => 'seller-product', 'name' => 'seller-product-create', 'display_name' => 'Product create'],
           ['module' => 'seller-product', 'name' => 'seller-product-edit', 'display_name' => 'Product edit'],
           ['module' => 'seller-product', 'name' => 'seller-product-delete', 'display_name' => 'Product delete'],
           ['module' => 'buyer-product-list', 'name' => 'buyer-product-list', 'display_name' => 'Product List'],
           ['module' => 'buyer-product-detail', 'name' => 'buyer-product-detail', 'display_name' => 'Product detail'],
           ['module' => 'inquery', 'name' => 'inquery', 'display_name' => 'Inquery page']
        ];


        foreach ($permissions as $permission) {
             Permission::create($permission);
        }
    }
}
