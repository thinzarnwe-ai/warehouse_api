<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zones = [];

        foreach (range('A', 'Z') as $index => $letter) {
            $zones[] = [
                'id' => $index + 1,
                'name' => $letter,
            ];
        }

        DB::table('zones')->insert($zones);
    }
}
