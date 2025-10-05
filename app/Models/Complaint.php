<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    protected $table = 'complaints';
    protected $keyType = 'string'; // UUID string
    public $incrementing = false;

    protected $fillable = ['id', 'title', 'description', 'housing_id', 'category_code', 'status_code', 'user_id', 'submitted_at', 'updated_by'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Casting
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'submitted_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    // 🔗 Relasi ke kategori (pakai code, bukan id)
    public function category()
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_code', 'code');
    }

    // 🔗 Relasi ke status (pakai code, bukan id)
    public function status()
    {
        return $this->belongsTo(ComplaintStatus::class, 'status_code', 'code');
    }

    // 🔗 Relasi ke housing
    public function housing()
    {
        return $this->belongsTo(Housing::class, 'housing_id');
    }

    // 🔗 Relasi ke user pembuat
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ganti nama function admin() jadi:
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
