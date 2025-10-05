<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Due extends Model
{
    use HasUuids;

    protected $table = 'dues';

    protected $guarded = [];
}
