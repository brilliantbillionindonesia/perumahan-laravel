<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FamilyDocument extends Model
{
    use HasUuids;

    protected $table = 'family_documents';
    protected $guarded = [];
}
