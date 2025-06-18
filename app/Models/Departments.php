<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Positions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departments extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function positions()
    {
        return $this->hasMany(Positions::class, 'department_id', 'id');
    }
}
