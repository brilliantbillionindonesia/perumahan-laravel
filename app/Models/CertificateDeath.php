<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateDeath extends Model
{
    protected $table = 'death_certificates';
    protected $fillable = [
        'id',
        'citizen_id',
        'date_of_death',
        'certificate_number',
        'document',
    ];
}
