<?php

namespace App\Models;

use App\Models\User;
use App\Models\Departments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_code', 'branch_name', 'branch_short_name'
    ];

    public function departments()
    {
        return $this->hasMany(Departments::class, 'branch_id', 'id');
    }

    public function Users()
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }
}
