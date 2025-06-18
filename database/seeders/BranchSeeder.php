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
        Branch::factory()->create([ 'branch_name' => 'Head Office','branch_code' => 'MM-001','branch_short_name'=>'HO']);
        Branch::factory()->create([ 'branch_name' => 'Lanthit','branch_code' => 'MM-101','branch_short_name'=>'LAN1']);
        Branch::factory()->create([ 'branch_name' => 'Theik Pan','branch_code' => 'MM-102','branch_short_name'=>'MDY1']);
        Branch::factory()->create([ 'branch_name' => 'Satsan','branch_code' => 'MM-103','branch_short_name'=>'SAT1']);
        Branch::factory()->create([ 'branch_name' => 'East Dagon','branch_code' => 'MM-104','branch_short_name'=>'EDG1']);
        Branch::factory()->create([ 'branch_name' => 'Mawlamyine','branch_code' => 'MM-105','branch_short_name'=>'MLM1']);
        Branch::factory()->create([ 'branch_name' =>'Tampawady','branch_code' => 'MM-106','branch_short_name'=>'MDY2']);
        Branch::factory()->create([ 'branch_name' =>'Hlaingtharyar','branch_code' => 'MM-107','branch_short_name'=>'HTY1']);
        Branch::factory()->create([ 'branch_name' =>'Ayetharyar','branch_code' => 'MM-108','branch_short_name'=>'ATY1']);
        Branch::factory()->create([ 'branch_name' =>'Bago','branch_code' => 'MM-110','branch_short_name'=>'BGO1']);
        Branch::factory()->create([ 'branch_name' =>'PRO1 PLUS (Terminal M)','branch_code' => 'MM-112','branch_short_name'=>'PTMN1']);
        Branch::factory()->create([ 'branch_name' =>'South Dagon','branch_code' => 'MM-113','branch_short_name'=>'SDG1']);
        Branch::factory()->create([ 'branch_name' =>'Shwe Pyi Thar','branch_code'=> 'MM-114','branch_short_name'=>'SPT1']);
        Branch::factory()->create([ 'branch_name' =>'Project Sales','branch_code' => 'MM-201','branch_short_name'=>'PRJ1']);
        Branch::factory()->create([ 'branch_name' =>'Online Sales','branch_code' => 'MM-202','branch_short_name'=>'ONS1']);
        Branch::factory()->create([ 'branch_name' =>'Whole Sales','branch_code' => 'MM-203','branch_short_name'=>'OLS1']);
        Branch::factory()->create([ 'branch_name'  =>'Clearance Sale','branch_code'=> 'MM-205','branch_short_name'=>'OLS2']);
        Branch::factory()->create([ 'branch_name' =>'WH-Myo Houng','branch_code' => 'MM-504','branch_short_name'=>'WHMH']);
        Branch::factory()->create([ 'branch_name' =>'WH-Mingalardon','branch_code' => 'MM-505','branch_short_name'=>'WHMLD']);
        Branch::factory()->create([ 'branch_name' =>'DC-Myawaddy','branch_code' => 'MM-509','branch_short_name'=>'DCMWD']);
        Branch::factory()->create([ 'branch_name' =>'DC-Mingalardon2','branch_code' => 'MM-510','branch_short_name'=>'DCMLD2']);

    }
}
