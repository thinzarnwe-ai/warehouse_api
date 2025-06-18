<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBranch extends Model
{
    use HasFactory;
    protected $fillable = ['branch_id','user_id'];

    public function branch(){
        return $this->belongsTo('App\Models\Branch','branch_id');
    }
}
