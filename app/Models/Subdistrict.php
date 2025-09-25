<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subdistrict extends Model
{
    protected $table = 'subdistricts';

    protected $fillable = [
        'id', 'code', 'name', 'district_code',
    ];

}
