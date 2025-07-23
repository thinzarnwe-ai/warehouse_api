<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::factory()->create([ 'branch_name' => 'Lanthit','branch_code' => 'MM-101','branch_short_name'=>'LAN']);
        Branch::factory()->create([ 'branch_name' => 'Theik Pan','branch_code' => 'MM-102','branch_short_name'=>'TP']);
        Branch::factory()->create([ 'branch_name' => 'Satsan','branch_code' => 'MM-103','branch_short_name'=>'SAT']);
        Branch::factory()->create([ 'branch_name' => 'East Dagon','branch_code' => 'MM-104','branch_short_name'=>'EDG']);
        Branch::factory()->create([ 'branch_name' => 'Mawlamyine','branch_code' => 'MM-105','branch_short_name'=>'MLM']);
        Branch::factory()->create([ 'branch_name' =>'Tampawady','branch_code' => 'MM-106','branch_short_name'=>'TPW']);
        Branch::factory()->create([ 'branch_name' =>'Hlaingtharyar','branch_code' => 'MM-107','branch_short_name'=>'HTY']);
        Branch::factory()->create([ 'branch_name' =>'Ayetharyar','branch_code' => 'MM-108','branch_short_name'=>'ATY']);
        Branch::factory()->create([ 'branch_name' =>'Bago','branch_code' => 'MM-110','branch_short_name'=>'BGO']);
        Branch::factory()->create([ 'branch_name' =>'PRO1 PLUS (Terminal M)','branch_code' => 'MM-112','branch_short_name'=>'PTMN']);
        Branch::factory()->create([ 'branch_name' =>'South Dagon','branch_code' => 'MM-113','branch_short_name'=>'SDG']);
        Branch::factory()->create([ 'branch_name' =>'Shwe Pyi Thar','branch_code'=> 'MM-114','branch_short_name'=>'DNG']);
        Branch::factory()->create([ 'branch_name' =>'Nay Pyi Taw','branch_code' => 'MM-115','branch_short_name'=>'NPT']);
        Branch::factory()->create([ 'branch_name' =>'Mingalardon','branch_code' => 'MM-109','branch_short_name'=>'MLD']);
        Branch::factory()->create([ 'branch_name' =>'DC-Mingalardon1   ','branch_code' => 'MM-505','branch_short_name'=>'DC1']);
        Branch::factory()->create([ 'branch_name' =>'DC-Mingalardon2','branch_code' => 'MM-510','branch_short_name'=>'DC2']);
        Branch::factory()->create([ 'branch_name' =>'DC-Mingalardon3','branch_code' => 'MM-511','branch_short_name'=>'DC3']);

    }
}
