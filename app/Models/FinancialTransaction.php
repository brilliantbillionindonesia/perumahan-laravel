<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasUuids;

    protected $table = 'financial_transactions';
    protected $guarded = [];
}
