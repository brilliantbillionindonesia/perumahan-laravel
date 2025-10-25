<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasUuids;

    protected $table = 'citizens';
    protected $fillable = [
        'family_card_id',
        'citizen_card_number',
        'fullname',
        'birth_place',
        'birth_date',
        'blood_type',
        'religion',
        'marital_status',
        'work_type',
        'education_type',
        'citizenship',
        'created_at',
        'updated_at'
    ];

    public function housingUsers()
    {
        return $this->hasMany(HousingUser::class, 'citizen_id');
    }
}
