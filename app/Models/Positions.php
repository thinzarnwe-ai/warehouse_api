<?php

namespace App\Models;

use App\Models\Departments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Positions extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'department_id'
    ];


    public function department()
    {
        return $this->belongsTo(Departments::class,'department_id','id');
    }
}
