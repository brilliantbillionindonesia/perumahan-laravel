<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousingUser extends Model
{
    protected $table = 'housing_users';
    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_code', 'code');
    }

    public function hasRole(string $code): bool
    {
        return $this->role && $this->role->code === $code;
    }

    public function hasPermission(string $permissionCode): bool
    {
        return $this->role
            && $this->role->permissions()->where('code', $permissionCode)->exists();
    }
}
