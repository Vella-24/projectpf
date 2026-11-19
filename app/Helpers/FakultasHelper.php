<?php

namespace App\Helpers;

class FakultasHelper
{
    /**
     * Pemetaan 2 digit kode awalan NRP ke Nama Fakultas di Universitas Surabaya (UBAYA)
     */
    public const DAFTAR = [
        '11' => 'Fakultas Farmasi',
        '12' => 'Fakultas Hukum',
        '13' => 'Fakultas Bisnis dan Ekonomika',
        '14' => 'Politeknik Ubaya',
        '15' => 'Fakultas Psikologi',
        '16' => 'Fakultas Teknik',
        '17' => 'Fakultas Teknobiologi',
        '18' => 'Fakultas Industri Kreatif',
        '19' => 'Fakultas Kedokteran',
    ];

    /**
     * Mengambil daftar semua fakultas
     */
    public static function getAll(): array
    {
        return self::DAFTAR;
    }

    /**
     * Mendapatkan nama fakultas dari kode (11-19 atau umum)
     */
    public static function getNamaByKode(?string $kode, bool $fullUmum = false): string
    {
        if (empty($kode) || $kode === 'umum') {
            return $fullUmum ? 'Umum (Semua Fakultas)' : 'Umum';
        }

        return self::DAFTAR[$kode] ?? ($fullUmum ? 'Umum (Semua Fakultas)' : 'Umum');
    }

    /**
     * Mendapatkan kode fakultas dari nomor NRP 9 digit (2 digit awal)
     */
    public static function getKodeFromNrp(?string $nrp): ?string
    {
        if (empty($nrp)) {
            return null;
        }

        $clean = preg_replace('/\D/', '', (string) $nrp);
        if (strlen($clean) >= 2) {
            $prefix = substr($clean, 0, 2);
            if (isset(self::DAFTAR[$prefix])) {
                return $prefix;
            }
        }

        return null;
    }

    /**
     * Mendapatkan info fakultas dari NRP: ['kode' => ..., 'nama' => ..., 'nrp' => ...]
     */
    public static function getInfoFromNrp(?string $nrp): ?array
    {
        $kode = self::getKodeFromNrp($nrp);
        if (!$kode) {
            return null;
        }

        return [
            'kode' => $kode,
            'nama' => self::DAFTAR[$kode],
            'nrp' => preg_replace('/\D/', '', (string) $nrp),
        ];
    }

    /**
     * Cek apakah kode fakultas valid
     */
    public static function isValidKode(?string $kode): bool
    {
        if ($kode === 'umum') {
            return true;
        }
        return isset(self::DAFTAR[$kode]);
    }

    /**
     * CSS class styling untuk badge fakultas
     */
    public static function getBadgeClass(?string $kode): string
    {
        if (empty($kode) || $kode === 'umum') {
            return 'bg-slate-100 text-slate-700 border-slate-200';
        }

        $colors = [
            '11' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            '12' => 'bg-red-50 text-red-700 border-red-200',
            '13' => 'bg-blue-50 text-blue-700 border-blue-200',
            '14' => 'bg-amber-50 text-amber-700 border-amber-200',
            '15' => 'bg-purple-50 text-purple-700 border-purple-200',
            '16' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
            '17' => 'bg-teal-50 text-teal-700 border-teal-200',
            '18' => 'bg-pink-50 text-pink-700 border-pink-200',
            '19' => 'bg-rose-50 text-rose-700 border-rose-200',
        ];

        return $colors[$kode] ?? 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
