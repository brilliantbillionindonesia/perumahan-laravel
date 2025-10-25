<?php

namespace App\Constants;

class EducationTypeOption
{
    public const TIDAK_SEKOLAH = 'Tidak Sekolah';
    public const SD = 'SD';
    public const SMP = 'SMP';
    public const SMA = 'SMA/SMK/MA';
    public const DIPLOMA_1 = 'Diploma I';
    public const DIPLOMA_2 = 'Diploma II';
    public const DIPLOMA_3 = 'Diploma III (D3)';
    public const DIPLOMA_4 = 'Diploma IV (D4) / Strata I';
    public const SARJANA = 'Sarjana (S1)';
    public const MAGISTER = 'Magister (S2)';
    public const DOKTOR = 'Doktor (S3)';
    public const LAINNYA = 'Lainnya';

    public static function getTypeOption($education_type)
    {
        $education_type = strtolower($education_type);
        switch ($education_type) {
            case 'tidak sekolah':
            case 'belum sekolah':
            case 'belum/tidak sekolah':
            case 'tidak/belum sekolah':
            case 'belum pernah sekolah':
                return self::TIDAK_SEKOLAH;
            case 'sd':
            case 'sd/sederajat':
            case 'sd / sederajat':
            case 'tamat sd':
            case 'tamat sd / sederajat':
            case 'madrasah ibtidaiyah':
            case 'mi':
                return self::SD;
            case 'smp':
            case 'smp/sederajat':
            case 'smp / sederajat':
            case 'sltp':
            case 'sltp/sederajat':
            case 'madrasah tsanawiyah':
            case 'mts':
                return self::SMP;
            case 'sma':
            case 'smk':
            case 'sma/sederajat':
            case 'sma / sederajat':
            case 'slta':
            case 'slta/sederajat':
            case 'slta / sederajat':
            case 'madrasah aliyah':
            case 'ma':
                return self::SMA;
            case 'd1':
            case 'diploma 1':
            case 'diploma i':
                return self::DIPLOMA_1;
            case 'd2':
            case 'diploma 2':
            case 'diploma ii':
                return self::DIPLOMA_2;
            case 'd3':
            case 'diploma 3':
            case 'diploma iii':
            case 'akademi':
            case 'akademi/diploma iii/sarjana muda':
            case 'sarjana muda':
                return self::DIPLOMA_3;
            case 'd4':
            case 'diploma 4':
            case 'diploma iv':
                return self::DIPLOMA_4;
            case 's1':
            case 's-1':
            case 'sarjana':
            case 'sarjana 1':
            case 'strata 1':
            case 'strata i':
            case 'diploma iv/strata i':
            case 'diploma iv / strata i':
            case 'sarjana strata 1':
                return self::SARJANA;
            case 's2':
            case 's-2':
            case 'magister':
            case 'strata 2':
            case 'strata ii':
            case 'sarjana strata 2':
                return self::MAGISTER;
            case 's3':
            case 's-3':
            case 'doktor':
            case 'strata 3':
            case 'strata iii':
            case 'dr.':
            case 'dr':
            case 'phd':
                return self::DOKTOR;
            default:
                return self::LAINNYA;
        }
    }
}
