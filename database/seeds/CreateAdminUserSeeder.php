<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('123456'),
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        $role = Role::create(['name' => 'admin', 'name_jp' => '管理者']);

        $permissions = Permission::pluck('id', 'id')->all();

        // $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);

        $otherRoles = [
            [
                'name' => 'operator',
                'name_jp' => '運営者'
            ],
            [
                'name' => 'seller',
                'name_jp' => '事業者'
            ],
            [
                'name' => 'buyer',
                'name_jp' => 'バイヤー'
            ],
            [
                'name' => 'vendor',
                'name_jp' => '地域商社'
            ]
        ];
        foreach($otherRoles as $role)
        {
            Role::create($role);
        }

        $otherUsers = [
            [
                'name' => 'Operator', 
                'email' => 'operator@mail.com',
                'password' => bcrypt('123456'),
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'name' => 'Seller',
                'email' => 'seller@mail.com',
                'island_id' => 1,
                'password' => bcrypt('123456'),
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'name' => 'Buyer',
                'email' => 'buyer@gmail.com',
                'password' => bcrypt('123456'),
                'created_by' => '1',
                'updated_by' => '1'
            ],
            [
                'name' => 'Vendor',
                'email' => 'vendor@mail.com',
                'password' => bcrypt('123456'),
                'created_by' => '1',
                'updated_by' => '1'
            ]
        ];

        foreach($otherUsers as $user)
        {
            $newUser = User::create($user);
            $newUser->assignRole($newUser->id);
        }

        // // buyers users
        // $buyerUsers = [
        //     [
        //         'name' => 'buyer1',
        //         'island_id' => 5,
        //         'email' => 'buyer1@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_at' => '2018-01-15 02:34:59',
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'buyer2',
        //         'island_id' => 4,
        //         'email' => 'buyer2@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'buyer3',
        //         'island_id' => 5,
        //         'email' => 'buyer3@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'buyer4',
        //         'island_id' => 5,
        //         'email' => 'buyer4@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'buyer5',
        //         'island_id' => 6,
        //         'email' => 'buyer5@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'buyer6',
        //         'island_id' => 3,
        //         'email' => 'buyer6@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'buyer7',
        //         'island_id' => 5,
        //         'email' => 'buyer7@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ]
        // ];

        // foreach($buyerUsers as $user)
        // {
        //     $newUser = User::create($user);
        //     $newUser->assignRole(4);
        // }

        // // sellers users
        // $sellerUsers = [
        //     [
        //         'name' => 'seller1',
        //         'island_id' => 5,
        //         'email' => 'seller1@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'seller2',
        //         'island_id' => 4,
        //         'email' => 'seller2@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_at' => '2019-05-15 02:34:59',
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'seller3',
        //         'island_id' => 5,
        //         'email' => 'seller3@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'seller4',
        //         'island_id' => 5,
        //         'email' => 'seller4@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'seller5',
        //         'island_id' => 6,
        //         'email' => 'seller5@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'seller6',
        //         'island_id' => 3,
        //         'email' => 'seller6@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ],
        //     [
        //         'name' => 'seller7',
        //         'island_id' => 5,
        //         'email' => 'seller7@mail.com',
        //         'password' => bcrypt('123456'),
        //         'created_by' => '2',
        //         'updated_by' => '2'
        //     ]
        // ];

        // foreach($sellerUsers as $user)
        // {
        //     $newUser = User::create($user);
        //     $newUser->assignRole(3);
        // }

        // // Roles with permission
        //admin
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo([
            'role-edit',
            'activity-log',
            'account-unblock',
            'all-account-list',
            'all-account-edit',
            'all-account-delete',
            'operator-create'
        ]);
        //operator
        $operatorRole = Role::findByName('operator');
        $operatorRole->givePermissionTo([
            'buyer-list',
            'buyer-create',
            'buyer-edit',
            'buyer-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',
            'island-list',
            'island-create',
            'island-edit',
            'island-delete',
            'seller-list',
            'seller-create',
            'seller-edit',
            'seller-delete'
        ]);
        //seller
        $sellerRole = Role::findByName('seller');
        $sellerRole->givePermissionTo([
            'seller-buyer-list',
            'seller-buyer-create',
            'seller-buyer-edit',
            'seller-buyer-delete',
            'seller-product-list',
            'seller-product-create',
            'seller-product-edit',
            'seller-product-delete',
            'inquery'
        ]);
        //buyer
        $buyerRole = Role::findByName('buyer');
        $buyerRole->givePermissionTo([
            'buyer-product-list',
            'buyer-product-detail',
            'inquery'
        ]);
    }
}
