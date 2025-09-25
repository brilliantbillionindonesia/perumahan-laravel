<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';
    protected $fillable = [
        'province_code',
        'code',
        'name',
        'created_at',
        'updated_at'
    ];
}
