<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyDocument extends Model
{
    protected $table = 'family_documents';
    protected $fillable = [
        'family_card_id',
        'doc_name',
        'doc_file',
        'created_at',
        'updated_at'
    ];
}
