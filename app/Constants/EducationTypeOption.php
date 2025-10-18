<?php

namespace App\Constants;

class EducationTypeOption
{
    public const TIDAK_SEKOLAH   = 'Tidak Sekolah';
    public const SD              = 'SD';
    public const SMP             = 'SMP';
    public const SMA             = 'SMA/SMK/MA';
    public const DIPLOMA_1       = 'Diploma I';
    public const DIPLOMA_2       = 'Diploma II';
    public const DIPLOMA_3       = 'Diploma III (D3)';
    public const DIPLOMA_4       = 'Diploma IV (D4)';
    public const SARJANA         = 'Sarjana (S1)';
    public const MAGISTER        = 'Magister (S2)';
    public const DOKTOR          = 'Doktor (S3)';

    public static function getTypeOption($education_type){
        $education_type = strtolower($education_type);
        switch ($education_type) {
            case 'tidak sekolah':
                return self::TIDAK_SEKOLAH;
            case 'sd':
                return self::SD;
            case 'smp':
                return self::SMP;
            case 'sma':
                return self::SMA;
            case 'diploma 1':
                return self::DIPLOMA_1;
            case 'diploma 2':
                return self::DIPLOMA_2;
            case 'diploma 3':
                return self::DIPLOMA_3;
            case 'diploma 4':
                return self::DIPLOMA_4;
            case 'sarjana':
                return self::SARJANA;
            case 'magister':
                return self::MAGISTER;
            case 'doktor':
                return self::DOKTOR;
            default:
                return self::TIDAK_SEKOLAH;
        }
    }
}
