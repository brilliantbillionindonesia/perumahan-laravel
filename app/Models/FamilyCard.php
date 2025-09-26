<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FamilyCard extends Model
{
    use HasUuids;

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
