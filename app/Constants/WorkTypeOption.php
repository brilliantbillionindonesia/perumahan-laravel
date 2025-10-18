<?php

namespace App\Constants;

class WorkTypeOption
{
    public const PELAJAR     = 'Pelajar/Mahasiswa';
    public const PNS         = 'Pegawai Negeri Sipil';
    public const TNI         = 'Tentara Nasional Indonesia';
    public const POLRI       = 'Kepolisian RI';
    public const KARYAWAN    = 'Karyawan Swasta';
    public const WIRASWASTA  = 'Wiraswasta';
    public const PETANI      = 'Petani';
    public const NELAYAN     = 'Nelayan';
    public const GURU        = 'Guru/Dosen';
    public const DOKTER      = 'Dokter/Bidan/Perawat';
    public const SOPIR       = 'Sopir';
    public const IBU_RUMAH_TANGGA = 'Ibu Rumah Tangga';
    public const PENSIUNAN   = 'Pensiunan';
    public const PENGANGGURAN = 'Belum/Tidak Bekerja';
    public const LAINNYA     = 'Lainnya';


    public static function getTypeOption($work_type){
        $work_type = strtolower($work_type);
        switch ($work_type) {
            case 'pelajar/mahasiswa':
                return self::PELAJAR;
            case 'pegawai negeri sipil':
                return self::PNS;
            case 'tentara nasional indonesia':
                return self::TNI;
            case 'kepolisian ri':
                return self::POLRI;
            case 'karyawan swasta':
                return self::KARYAWAN;
            case 'wiraswasta':
                return self::WIRASWASTA;
            case 'petani':
                return self::PETANI;
            case 'nelayan':
                return self::NELAYAN;
            case 'guru/dosen':
                return self::GURU;
            case 'dokter/bidan/perawat':
                return self::DOKTER;
            case 'sopir':
                return self::SOPIR;
            case 'ibu rumah tangga':
                return self::IBU_RUMAH_TANGGA;
            case 'pensiunan':
                return self::PENSIUNAN;
            case 'belum/tidak bekerja':
                return self::PENGANGGURAN;
            default:
                return self::LAINNYA;
        }
    }
}
