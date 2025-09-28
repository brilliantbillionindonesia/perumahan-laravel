<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $table = 'villages';
    protected $fillable = [
        'province_code',
        'district_code',
        'subdistrict_code',
        'code', 
        'name',

    ];
}
