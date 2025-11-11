<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyCardScannedDoc extends Model
{
    use SoftDeletes;

    protected $table = 'family_card_scanned_docs';
    protected $guarded = [];

    protected $casts = [
        'data_json' => 'json',
        'data_json_verified' => 'json',
    ];
}
