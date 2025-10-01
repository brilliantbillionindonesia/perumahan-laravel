<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintStatus extends Model
{
    protected $table = 'complaint_statuses';
    protected $fillable = [
        'name',
        'code'
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'status_id');
    }
}
