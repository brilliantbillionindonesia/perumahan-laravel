<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    protected $fillable = ['permission_code', 'role_code'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_roles', 'permission_code', 'role_code', 'code', 'code');
    }
}
