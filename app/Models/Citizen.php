<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
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
}
