<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $table = 'houses';
    protected $fillable = [
        'housing_id',
        'house_name',
        'block',
        'number',
        'family_card_id',
        'created_at',
        'updated_at'
    ];
}
