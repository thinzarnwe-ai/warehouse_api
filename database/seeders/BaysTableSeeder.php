<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [];

        for ($i = 1; $i <= 100; $i++) {
            $rows[] = [
                'id' => $i,
                'name' => str_pad($i, 3, '0', STR_PAD_LEFT),
            ];
        }

        DB::table('bays')->insert($rows);
    }
}
