<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patroling extends Model
{
    protected $table = 'patrollings';

    protected $guarded = [];

    protected $casts = [
        'patrol_date' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id', 'id');
    }

    public function housing()
    {
        return $this->belongsTo(Housing::class, 'housing_id', 'id');
    }

    public function replacedBy()
    {
        return $this->belongsTo(Citizen::class, 'replaced_by', 'id');
    }

    public function house()
    {
        return $this->belongsTo(House::class, 'house_id', 'id');
    }
}
