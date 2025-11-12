<?php

namespace App\Constants;

class BloodTypeOption
{
    public const A  = 'A';
    public const B  = 'B';
    public const AB = 'AB';
    public const O  = 'O';
    public const NA = '-';

    public static function getTypeOption($blood_type){
        $blood_type = strtoupper($blood_type);
        switch ($blood_type) {
            case 'A':
                return self::A;
            case 'B':
                return self::B;
            case 'AB':
                return self::AB;
            case 'O':
                return self::O;
            default:
                return self::NA;
        }
    }

}
