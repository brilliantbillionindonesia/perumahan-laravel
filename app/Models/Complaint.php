<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    protected $table = 'complaints';
    protected $keyType = 'string';   // karena UUID string
    public $incrementing = false;    // bukan auto-increment

    protected $fillable = [
        'id',
        'title',
        'description',
        'housing_id',
        'category_code',
        'status_code',
        'user_id',
        'submitted_at',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Casting field
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s', 
        'submitted_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime',
        'status_id' => 'integer',
        'category_id' => 'integer',
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