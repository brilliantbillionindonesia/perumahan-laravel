<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Housing extends Model
{
    protected $table = 'housings';
    protected $fillable = [
        'housing_name',
        'address',
        'rt',
        'rw',
        'subdistrict',
        'district',
        'province',
        'postal_code',
        'created_at',
        'updated_at'
    ];
}
