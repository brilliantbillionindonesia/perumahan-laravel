<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['code', 'name'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_code', 'permission_code', 'code', 'code');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_code', 'user_id');
    }
}
