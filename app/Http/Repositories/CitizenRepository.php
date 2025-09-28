<?php

namespace App\Http\Repositories;

use App\Constants\RelationshipStatusOption;
use DB;

class CitizenRepository
{

    public static function queryCitizen($housingId){
        $data = DB::table('housing_users as hu')
        ->select(
            'hu.id as housing_user_id',
            'fc.id as family_card_id',
            'hu.housing_id',
            'hs.id as house_id',
            'c.id as citizen_id',
            'c.fullname',
            'hu.user_id',
            'u.email',
            'hs.house_name',
            'hs.block',
            'hs.number',
            'fm.relationship_status'
        )
        ->join('citizens as c', 'hu.citizen_id', '=', 'c.id')
        ->join('family_cards as fc', 'c.family_card_id', '=', 'fc.id')
        ->leftJoin('houses as hs', function ($join) use ($housingId) {
            $join->on('c.family_card_id', '=', 'hs.family_card_id')
                ->where('hs.housing_id', '=', $housingId);
        })
        ->leftJoin('family_members as fm', 'fm.citizen_id', '=', 'c.id')
        ->leftJoin('users as u', 'u.id', '=', 'hu.user_id')
        ->where('hu.housing_id', $housingId)
        ->orderBy('c.fullname', 'asc');

        return $data;
    }
}
