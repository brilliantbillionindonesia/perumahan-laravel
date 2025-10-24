<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $guarded = [];

    protected $casts = [
        'data_json' => 'json',
    ];
}
