<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\UserBranch;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.p
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'title'                 =>'Miss.',
            'name'                  => 'Super Admin', 
            'emp_id'                => 'superadmin@mail.com',
            'status'                =>1,
            'branch_id'             =>1,
            'all_branch'            => 'on',
            'password' => Hash::make('admin123'),
        ]);
        $user->assignRole('Super-Admin');

         $branches = Branch::all();
          foreach ($branches as $branch) {
        UserBranch::create([
            'branch_id' => $branch->id,
            'user_id'   => $user->id,
        ]);
    }

    }
}

