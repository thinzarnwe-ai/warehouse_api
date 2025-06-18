<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\UserBranch;
use App\Models\SubDocument;
use App\Models\MainDocument;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CreateForOtherBranchLog;
use App\Models\Status;
use App\Models\StockCycleCount;

function getAuthUser()
{
    return Auth::guard()->user();
}


function formatted_date($date)
{
    return Carbon::parse($date)->format('Y-m-d');
}
function saveCoverPhoto($file)
{
    
        $folderName = "uploads/coverphoto" ;
        // dd($file->getClientOriginalName());
        $fileName = $file->getClientOriginalName();

        $originalFileName =  preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName);
        $saveFileName = $originalFileName .rand(1, 99999).".". $file->getClientOriginalExtension();
        $file->storeAs($folderName, $saveFileName);
        return $saveFileName;
}
