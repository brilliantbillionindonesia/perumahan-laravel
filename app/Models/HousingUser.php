<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousingUser extends Model
{
    protected $table = 'housing_users';
    protected $fillable = [
        'housing_id',
        'user_id',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
