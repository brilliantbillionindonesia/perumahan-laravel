<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    protected $table = 'religion';
    protected $fillable = [
        'name',
        'code',
        'created_at',
        'updated_at'
    ];
}
