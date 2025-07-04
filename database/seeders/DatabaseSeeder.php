<?php

namespace Database\Seeders;

use App\Models\Department;
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
        $this->call([
        BranchSeeder::class,
        PermissionSeeder::class,
        UserTableSeeder::class,
        ZonesTableSeeder::class,
        RowsTableSeeder::class,
        LevelTableSeeder::class,
        BaysTableSeeder::class,
    ]);
        // \App\Models\User::factory(10)->create();
    }
}
