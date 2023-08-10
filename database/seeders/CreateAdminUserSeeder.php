<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
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
            'name' => 'سعيد محمود سعيد العلول',
            'email' => 'saeedalaloul@gmail.com',
            'dob' => date('Y-m-d'),
            'phone' => '0599927057',
            'identification_number' => '123456784',
            'password' => bcrypt('12345678'),
        ]);

        $user->markEmailAsVerified();

        $roles = [
            ['name' => 'مشرف'],
            ['name' => 'محفظ'],
            ['name' => 'مختبر'],
            ['name' => 'مشرف الإختبارات'],
            ['name' => 'مشرف الرقابة'],
            ['name' => 'مشرف الأنشطة'],
            ['name' => 'مشرف الدورات'],
            ['name' => 'منشط'],
            ['name' => 'مراقب'],
            ['name' => 'طالب'],
            ['name' => 'ولي أمر الطالب'],
        ];

        foreach ($roles as $role){
            Role::create($role);
        }

        $role = Role::create(['name' => 'أمير المركز']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
