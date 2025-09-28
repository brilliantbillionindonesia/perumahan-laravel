<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $table = 'marital_status';
    protected $fillable = [
        'name',
        'code',
        'created_at',
        'updated_at'
    ];
}
