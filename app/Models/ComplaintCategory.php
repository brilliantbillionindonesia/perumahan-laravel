<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintCategory extends Model
{
    protected $table = 'complaint_categories';
    protected $guarded = [];

    // Relasi ke Complaint
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'category_id');
    }
}
