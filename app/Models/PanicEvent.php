<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PanicEvent extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function recipients()
    {
        return $this->hasMany(PanicRecipient::class, 'panic_event_id');
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
