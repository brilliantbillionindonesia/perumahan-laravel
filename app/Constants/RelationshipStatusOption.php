<?php

namespace App\Constants;

class RelationshipStatusOption
{
    public const KEPALA_KELUARGA   = 'Kepala Keluarga';
    public const SUAMI             = 'Suami';
    public const ISTRI             = 'Istri';
    public const ANAK              = 'Anak';
    public const MENANTU           = 'Menantu';
    public const CUCU              = 'Cucu';
    public const ORANG_TUA         = 'Orang Tua';
    public const MERTUA            = 'Mertua';
    public const FAMILI_LAIN       = 'Famili Lain';
    public const PEMBANTU          = 'Pembantu';
    public const LAINNYA           = 'Lainnya';

    public static function all(): array
    {
        return [
            self::KEPALA_KELUARGA,
            self::SUAMI,
            self::ISTRI,
            self::ANAK,
            self::MENANTU,
            self::CUCU,
            self::ORANG_TUA,
            self::MERTUA,
            self::FAMILI_LAIN,
            self::PEMBANTU,
            self::LAINNYA,
        ];
    }
}
