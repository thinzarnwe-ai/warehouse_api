<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductNameChangeLog extends Model
{
    use HasFactory;
    protected $table = 'productnamechangelog';
    protected $fillable = ['product_code','old_product_name','new_product_name','user_id']; 
}
