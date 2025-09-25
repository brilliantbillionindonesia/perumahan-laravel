<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyCard extends Model
{
    protected $table = 'family_cards';
    protected $fillable = [
        'number',
        'address',
        'rt',
        'rw',
        'village_code',
        'subdistrict_code',
        'district_code',
        'province_code',
        'postal_code',
        'created_at',
        'updated_at'
    ];
}
