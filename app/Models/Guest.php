<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasUuids;

    protected $table = 'guests';
    protected $guarded = [];
}
