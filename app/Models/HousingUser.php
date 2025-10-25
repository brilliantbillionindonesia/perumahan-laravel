<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HousingUser extends Model
{
    use HasUuids;

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

    public function housing()
    {
        return $this->belongsTo(Housing::class, 'housing_id');
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id');
    }
}
