<?php

namespace App\Constants;

class RelationshipStatusOption
{
    public const KEPALA_KELUARGA = 'Kepala Keluarga';
    public const SUAMI = 'Suami';
    public const ISTRI = 'Istri';
    public const ANAK = 'Anak';
    public const MENANTU = 'Menantu';
    public const CUCU = 'Cucu';
    public const ORANG_TUA = 'Orang Tua';
    public const MERTUA = 'Mertua';
    public const FAMILI_LAIN = 'Famili Lain';
    public const PEMBANTU = 'Pembantu';
    public const KANDUNG = 'Saudara Kandung';
    public const KEPONAKAN = 'Keponakan';
    public const TEMAN = 'Teman';
    public const LAINNYA = 'Lainnya';

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
            self::KANDUNG,
            self::KEPONAKAN,
            self::TEMAN,
            self::LAINNYA,
        ];
    }

    public static function getTypeOption($string)
    {
        $string = strtolower(trim($string));

        // Normalisasi karakter umum
        $string = str_replace(['-', '_', '.', '\\'], ' ', $string);
        $string = preg_replace('/\s+/', ' ', $string);

        switch (true) {

            // 🧑‍💼 Kepala Keluarga
            case str_contains($string, 'kepala'):
            case str_contains($string, 'kk'):
            case str_contains($string, 'kepala keluarga'):
            case str_contains($string, 'kepala rumah tangga'):
                return self::KEPALA_KELUARGA;

            // 👨 Suami
            case str_contains($string, 'suami'):
            case str_contains($string, 'husband'):
            case str_contains($string, 'bapak'):
                return self::SUAMI;

            // 👩 Istri
            case str_contains($string, 'istri'):
            case str_contains($string, 'wife'):
            case str_contains($string, 'ibu'):
                return self::ISTRI;

            // 👶 Anak
            case str_contains($string, 'anak'):
            case str_contains($string, 'putra'):
            case str_contains($string, 'putri'):
            case str_contains($string, 'son'):
            case str_contains($string, 'daughter'):
                return self::ANAK;

            // 💍 Menantu
            case str_contains($string, 'menantu'):
            case str_contains($string, 'daughter in law'):
            case str_contains($string, 'son in law'):
                return self::MENANTU;

            // 👦 Cucu
            case str_contains($string, 'cucu'):
            case str_contains($string, 'grandchild'):
                return self::CUCU;

            // 👵 Orang Tua
            case str_contains($string, 'orang tua'):
            case str_contains($string, 'ayah'):
            case str_contains($string, 'ibu kandung'):
            case str_contains($string, 'parent'):
                return self::ORANG_TUA;

            // 🧓 Mertua
            case str_contains($string, 'mertua'):
            case str_contains($string, 'father in law'):
            case str_contains($string, 'mother in law'):
                return self::MERTUA;

            // 👨‍👩‍👧‍👦 Famili Lain
            case str_contains($string, 'famili'):
            case str_contains($string, 'saudara'):
            case str_contains($string, 'kerabat'):
            case str_contains($string, 'sepupu'):
            case str_contains($string, 'keponakan'):
            case str_contains($string, 'adik'):
            case str_contains($string, 'kakak'):
                return self::FAMILI_LAIN;

            // 👩‍🍳 Pembantu
            case str_contains($string, 'pembantu'):
            case str_contains($string, 'asisten'):
            case str_contains($string, 'art'):
            case str_contains($string, 'helper'):
            case str_contains($string, 'housemaid'):
                return self::PEMBANTU;

            // ❓ Default → Lainnya
            default:
                return self::LAINNYA;
        }
    }
}
