<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [];

        for ($i = 1; $i < 100; $i++) {
            $rows[] = [
                'id' => $i,
                'name' => str_pad($i, 2, '0', STR_PAD_LEFT),
            ];
        }

        DB::table('levels')->insert($rows);
    }
}
