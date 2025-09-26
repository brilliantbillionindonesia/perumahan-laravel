<?php

namespace App\Http\Repositories;

use DB;

class HousingRepository
{
    public static function queryHousing($userId){
        return DB::table('housing_users as hu')
            ->join('housings as h', 'hu.housing_id', '=', 'h.id')
            ->join('roles as role', 'role.code', '=', 'hu.role_code')
            ->leftJoin('villages as v', 'v.code', '=', 'h.village_code')
            ->leftJoin('subdistricts as sdt', 'sdt.code', '=', 'h.subdistrict_code')
            ->leftJoin('districts as dst', 'dst.code', '=', 'h.district_code')
            ->leftJoin('provinces as prv', 'prv.code', '=', 'h.province_code')
            ->select(
                'hu.housing_id',
                'hu.user_id',
                'role.name as role_name',
                'hu.role_code',
                'h.housing_name',
                'h.address',
                'v.name as village_name',
                'sdt.name as subdistrict_name',
                'dst.name as district_name',
                'prv.name as province_name',
                'h.postal_code'
            )
            ->where('hu.user_id', $userId)
            ->where('hu.is_active', 1);
    }

    public static function queryHousingUser($housingId){
        return  DB::table('users as u')
        ->join('housing_users as hu', 'hu.user_id', '=', 'u.id')
        ->join('roles as role', 'role.code', '=', 'hu.role_code')
        ->where('hu.housing_id', $housingId)
        ->select(
            'hu.id',
            'u.name',
            'u.email',
            'hu.role_code',
            'u.email_verified_at',
            'role.name as role_name',
            'hu.is_active'
        );
    }

}
