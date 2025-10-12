<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PanicRecipient extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(PanicEvent::class, 'panic_event_id');
    }

    public function eventActive()
    {
        return $this->belongsTo(PanicEvent::class, 'panic_event_id')->where('status', 'active');
    }
}
