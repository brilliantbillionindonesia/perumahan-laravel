<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasUuids;

    protected $table = 'citizens';
    protected $guarded = [];

    public function housingUsers()
    {
        return $this->hasMany(HousingUser::class, 'citizen_id');
    }
}
