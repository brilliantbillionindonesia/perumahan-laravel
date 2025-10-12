<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasUuids;

    protected $guarded = [];
}
