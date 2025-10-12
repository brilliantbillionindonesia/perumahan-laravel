<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FinancialTransaction extends Model
{
    use HasUuids;

    protected $table = 'financial_transactions';
    protected $guarded = [];

    public function getEvidenceUrlAttribute()
    {
        return $this->evidence ? Storage::disk('public')->url($this->evidence) : null;
    }

    public function category(){
        return $this->belongsTo(FinancialCategory::class, 'financial_category_code', 'code');
    }
}
