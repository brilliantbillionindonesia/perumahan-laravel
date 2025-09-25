<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationType extends Model
{
    protected $table = 'education_types';
    protected $fillable = [
        'name',
        'code',
        'created_at',
        'updated_at'
    ];
}
