<?php

namespace App\Constants;

class CitizenshipOption
{
    public const WNI = 'WNI';
    public const WNA = 'WNA';

    public static function getTypeOption($citizenship){
        $citizenship = strtoupper($citizenship);
        switch ($citizenship) {
            case 'WNI':
            case 'INDONESIA':
                return self::WNI;
            case 'WNA':
                return self::WNA;
            default:
                return self::WNI;
        }

    }
}
