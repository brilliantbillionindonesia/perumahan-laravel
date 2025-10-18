<?php

namespace App\Constants;

class MaritalStatusOption
{
    public const BELUMKAWIN = 'Belum Kawin';
    public const KAWIN = 'Kawin';
    public const CERAIHIDUP = 'Cerai Hidup';
    public const CERAIMATI = 'Cerai Mati';
    public const NA = '-';

    public static function getTypeOption($marital_status){
        $marital_status = strtolower($marital_status);
        switch ($marital_status) {
            case 'belum menikah':
                return self::BELUMKAWIN;
            case 'menikah':
                return self::KAWIN;
            case 'cerai hidup':
                return self::CERAIHIDUP;
            case 'cerai mati':
                return self::CERAIMATI;
            default:
                return self::BELUMKAWIN;
        }
    }
}
