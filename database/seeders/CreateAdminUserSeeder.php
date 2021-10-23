<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            'name' => 'Saeed Alaloul',
            'email' => 'saeedalaloul@gmail.com',
            'dob' => date('Y-m-d'),
            'phone' => '0593654277',
            'identification_number' => '123456784',
            'password' => bcrypt('12345678'),
            'address' => 'ejneoo',
        ]);

        $role = Role::create(['name' => 'أمير المركز']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
