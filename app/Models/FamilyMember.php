<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasUuids;

    protected $table = 'family_members';
    protected $guarded = [];
}
