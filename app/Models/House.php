<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasUuids;

    protected $table = 'houses';
    protected $guarded = [];

    public function citizens()
    {
        return $this->hasManyThrough(
            Citizen::class,
            FamilyCard::class,
            'id',                // Foreign key on FamilyCard table
            'family_card_id',    // Foreign key on Citizen table
            'family_card_id',    // Local key on House table
            'id'                 // Local key on FamilyCard table
        );
    }

    public function housing()
    {
        return $this->belongsTo(Housing::class, 'housing_id', 'id');
    }

    public function familyCard()
    {
        return $this->belongsTo(FamilyCard::class, 'family_card_id', 'id');
    }
}
