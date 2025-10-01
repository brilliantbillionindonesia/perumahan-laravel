<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaints';

    protected $fillable = [
        'housing_id',
        'user_id',
        'title',
        'category_id',
        'description',
        'status_id',
        'updated_by',
        'note'
    ];

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    // Relasi ke status
    public function status()
    {
        return $this->belongsTo(ComplaintStatus::class, 'status_id');
    }

    // Relasi ke housing
    public function housing()
    {
        return $this->belongsTo(Housing::class, 'housing_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke user yang terakhir update
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}