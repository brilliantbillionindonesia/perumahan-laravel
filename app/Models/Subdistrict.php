<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    protected $table = 'subdistricts';
    protected $fillable = [
        'code',
        'name',
        'district_code',
        'created_at',
        'updated_at'
    ];
}
