<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subdistrict extends Model
{
    protected $table = 'subdistricts';

    protected $fillable = [
        'id', 'code', 'name', 'district_code',
    ];

    public $incrementing = false; // karena bukan auto increment
    protected $keyType = 'string'; // UUID = string

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // generate UUID otomatis
            }
        });
    }
}