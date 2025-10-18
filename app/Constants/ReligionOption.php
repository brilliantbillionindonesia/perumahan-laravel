<?php

namespace App\Constants;

class ReligionOption
{
    public const ISLAM     = 'Islam';
    public const KRISTEN   = 'Kristen Protestan';
    public const KATOLIK  = 'Katolik';
    public const HINDU     = 'Hindu';
    public const BUDDHA    = 'Buddha';
    public const KONGHUCU  = 'Konghucu';

    public const NA = "-";

    public static function getTypeOption($religion){
        $religion = strtolower($religion);

        switch ($religion) {
            case 'islam':
                return self::ISLAM;
            case 'kristen protestan':
            case 'kristen':
                return self::KRISTEN;
            case 'katolik':
                return self::KATOLIK;
            case 'hindu':
                return self::HINDU;
            case 'buddha':
                return self::BUDDHA;
            case 'konghucu':
                return self::KONGHUCU;
            default:
                return self::NA;
        }
    }
}
