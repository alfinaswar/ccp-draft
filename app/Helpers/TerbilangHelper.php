<?php

if (!function_exists('terbilang')) {
    /**
     * Mengkonversi angka menjadi terbilang dalam Bahasa Indonesia
     *
     * @param int|float $angka
     * @return string
     */
    function terbilang($angka)
    {
        $angka = abs($angka);
        $bilangan = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        ];

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return terbilang(floor($angka / 10)) . ' puluh ' . terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'seratus ' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return terbilang(floor($angka / 100)) . ' ratus ' . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return terbilang(floor($angka / 1000)) . ' ribu ' . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return terbilang(floor($angka / 1000000)) . ' juta ' . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return terbilang(floor($angka / 1000000000)) . ' miliar ' . terbilang($angka % 1000000000);
        } elseif ($angka < 1000000000000000) {
            return terbilang(floor($angka / 1000000000000)) . ' triliun ' . terbilang($angka % 1000000000000);
        }

        return '';
    }
}

if (!function_exists('terbilang_rupiah')) {
    /**
     * Mengkonversi angka menjadi terbilang rupiah
     *
     * @param int|float $angka
     * @return string
     */
    function terbilang_rupiah($angka)
    {
        $hasil = trim(terbilang($angka));
        return ucfirst($hasil) . ' rupiah';
    }
}

if (!function_exists('format_angka_indonesia')) {
    /**
     * Format angka dengan pemisah ribuan dan desimal ala Indonesia
     *
     * @param int|float $angka
     * @param int $desimal
     * @return string
     */
    function format_angka_indonesia($angka, $desimal = 0)
    {
        return number_format($angka, $desimal, ',', '.');
    }
}
