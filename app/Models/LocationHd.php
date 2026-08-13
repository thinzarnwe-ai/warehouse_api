<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationHd extends Model
{
    protected $connection = 'cycan_scan';

    protected $table = 'masterdata.location_hd';

    protected $primaryKey = 'location_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'location_code',
        'location_date',
        'branch_code',
        'location_status_code',
        'location_type_sub_code',
        'location_bank_code',
        'location_remark',
        'location_shelf_id',
        'location_bay_id',
        'location_empsave',
        'savetime',
        'category_code',
        'type_code',
        'flag_new',
        'code_new',
        'level_qty',
        'update_time',
        'local_short_name',
        'group_code',
    ];

    protected $casts = [
        'flag_new' => 'boolean',
        'location_date' => 'datetime',
        'savetime' => 'datetime',
        'update_time' => 'datetime',
    ];
}
