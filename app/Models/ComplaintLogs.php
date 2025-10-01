<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintLogs extends Model
{
    protected $table = 'complaint_logs';
    protected $fillable = [
        'complaint_id',
        'logged_by',
        'logged_at',
        'status_code',
        'note'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
