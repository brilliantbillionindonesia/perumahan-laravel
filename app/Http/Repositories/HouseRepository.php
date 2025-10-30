<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class HouseRepository
{
    public static function queryHouse(){
        $housingId = request()->current_housing->housing_id;
        $houses = DB::table('houses')
        ->select(
            'houses.id',
            'houses.house_name',
            'houses.block',
            'houses.number',
            'houses.family_card_id',
            'chead.fullname as head_name'
        )
        ->join('citizens as chead', 'houses.head_citizen_id', '=', 'chead.id')
        ->where('houses.housing_id', $housingId);

        return $houses;
    }
}
