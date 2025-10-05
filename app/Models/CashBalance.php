<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CashBalance extends Model
{
    use HasUuids;

    protected $table = 'cash_balances';

    protected $guarded = [];
}
