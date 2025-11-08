<?php

namespace App\Constants;

class GenderOption
{
    public const LAKILAKI = 'Laki-Laki';
    public const PEREMPUAN = 'Perempuan';

    public static function getTypeOption($gender){
        $gender = strtolower($gender);
        if($gender == "laki-laki" || $gender == "l"){
            return self::LAKILAKI;
        }

        if($gender == "perempuan" || $gender == "p"){
            return self::PEREMPUAN;
        }
        return null;
    }
}
