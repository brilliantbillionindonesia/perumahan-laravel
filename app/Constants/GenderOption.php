<?php

namespace App\Constants;

class GenderOption
{
    public const LAKILAKI = 'Laki-laki';
    public const PEREMPUAN = 'Perempuan';

    public static function getTypeOption($gender){
        $gender = strtolower($gender);
        if($gender == "laki-laki"){
            return self::LAKILAKI;
        }

        if($gender == "perempuan"){
            return self::PEREMPUAN;
        }
        return null;
    }
}
