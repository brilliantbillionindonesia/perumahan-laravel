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

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_code', 'code');
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class, 'subdistrict_code', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function citizens()
    {
        return $this->hasMany(Citizen::class, 'family_card_id', 'id');
    }
}
