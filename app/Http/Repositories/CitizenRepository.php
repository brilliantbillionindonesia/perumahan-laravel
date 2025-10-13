<?php

namespace App\Http\Repositories;

use App\Constants\RelationshipStatusOption;
use DB;

class CitizenRepository
{

    public static function queryCitizen(){
        $role = request()->current_housing->role_code;
        $housingId = request()->current_housing->housing_id;
        $selectRaw = '
            hu.id AS housing_user_id,
            fc.id AS family_card_id,
            hu.housing_id,
            hs.id AS house_id,
            c.id AS citizen_id,
            c.fullname,
            hu.user_id,
            hs.house_name,
            hs.block,
            hs.`number`,
            c.religion,
            c.gender,
            c.work_type
        ';

        if($role != 'citizen'){
            $selectRaw = $selectRaw . ',
            u.email,
            fm.relationship_status,
            c.birth_place,
            c.blood_type,
            c.education_type,
            c.birth_date';
        }

        $data = DB::table('housing_users as hu')
        ->selectRaw($selectRaw)
        ->join('citizens as c', 'hu.citizen_id', '=', 'c.id')
        ->join('family_cards as fc', 'c.family_card_id', '=', 'fc.id')
        ->leftJoin('houses as hs', function ($join) use ($housingId) {
            $join->on('c.family_card_id', '=', 'hs.family_card_id')
                ->where('hs.housing_id', '=', $housingId);
        })
        ->leftJoin('family_members as fm', 'fm.citizen_id', '=', 'c.id')
        ->leftJoin('users as u', 'u.id', '=', 'hu.user_id')
        ->where('hu.housing_id', $housingId)
         ->where('hu.is_active', 1)
        ->orderBy('c.fullname', 'asc');

        return $data;
    }
}
