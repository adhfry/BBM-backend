<?php
namespace App\Services\Linguistics;

class FsaSyllableService
{
    // Mengganti global variable $pola menjadi property class private
    private array $pola      = [];
    private array $vocal     = ["a", "â", "e", "è", "i", "o", "u", "'", "é", "ê"];
    private array $konsonan2 = ["b", "g", "d", "j", "k"];

    /**
     * Memproses FSA Tingkat Satu (Mengenali pola V, K, KV, Glotal)
     */
    private function fsaTingkatSatu(string $kata): string
    {
        $kata = mb_strtolower($kata);
        $kata = str_replace(['\\', '-'], '', $kata);

        // Memecah kata menjadi array huruf secara multibyte-safe (UTF-8)
        // Ini menghindari masalah utf8_decode() di kode lama
        $huruf       = mb_str_split($kata);
        $panjangKata = count($huruf);

        $hasil1 = "";
        $i      = 0;

        while ($i < $panjangKata) {
            $hurufSekarang     = $huruf[$i];
            $hurufSelanjutnya1 = $huruf[$i + 1] ?? '';
            $hurufSelanjutnya2 = $huruf[$i + 2] ?? '';

            if ($hurufSekarang === "n") {
                if (($hurufSelanjutnya1 === "g" || $hurufSelanjutnya1 === "y") && in_array($hurufSelanjutnya2, $this->vocal)) {
                    $hasil1       .= $hurufSekarang . $hurufSelanjutnya1 . $hurufSelanjutnya2;
                    $this->pola[]  = 3;
                    $i            += 2;
                } elseif ($hurufSelanjutnya1 === "g" || $hurufSelanjutnya1 === "y") {
                    $hasil1       .= $hurufSekarang . $hurufSelanjutnya1;
                    $this->pola[]  = 2;
                    $i            += 1;
                } else {
                    if (in_array($hurufSelanjutnya1, $this->vocal)) {
                        $hasil1       .= $hurufSekarang . $hurufSelanjutnya1;
                        $this->pola[]  = 3;
                        $i            += 1;
                    } else {
                        $hasil1       .= $hurufSekarang;
                        $this->pola[]  = 2;
                    }
                }
                $hasil1 .= "-";
            } elseif (in_array($hurufSekarang, $this->konsonan2)) {
                if ($hurufSelanjutnya1 === "h" && in_array($hurufSelanjutnya2, $this->vocal)) {
                    $hasil1       .= $hurufSekarang . $hurufSelanjutnya1 . $hurufSelanjutnya2;
                    $this->pola[]  = 3;
                    $i            += 2;
                } elseif ($hurufSelanjutnya1 === "h") {
                    $hasil1       .= $hurufSekarang . $hurufSelanjutnya1;
                    $this->pola[]  = 2;
                    $i            += 1;
                } elseif (in_array($hurufSelanjutnya1, $this->vocal)) {
                    $hasil1       .= $hurufSekarang . $hurufSelanjutnya1;
                    $this->pola[]  = 3;
                    $i            += 1;
                } else {
                    $hasil1       .= $hurufSekarang;
                    $this->pola[]  = 2;
                }
                $hasil1 .= "-";
            } elseif (in_array($hurufSekarang, $this->vocal)) {
                $hasil1 .= $hurufSekarang . "-";
                if ($hurufSekarang === "'") {
                    $this->pola[] = 4;
                } else {
                    $this->pola[] = 1;
                }
            } else {
                if (in_array($hurufSelanjutnya1, $this->vocal)) {
                    $hasil1       .= $hurufSekarang . $hurufSelanjutnya1;
                    $this->pola[]  = 3;
                    $i            += 1;
                } else {
                    $hasil1       .= $hurufSekarang;
                    $this->pola[]  = 2;
                }
                $hasil1 .= "-";
            }
            $i++;
        }

        return $this->fsaTingkatDua($hasil1);
    }

    /**
     * Memproses FSA Tingkat Dua (Penggabungan suku kata)
     */
    private function fsaTingkatDua(string $kata): string
    {
        $arrSkt      = explode('-', $kata);
        $i           = 0;
        $hasil2      = "";
        $jumlahArray = count($arrSkt);

        while ($i < $jumlahArray - 1) {
            $polaSekarang     = $this->pola[$i] ?? null;
            $polaSelanjutnya1 = $this->pola[$i + 1] ?? null;
            $polaSelanjutnya2 = $this->pola[$i + 2] ?? null;

            if ($polaSekarang === 1 && ($polaSelanjutnya1 === 2 || $polaSelanjutnya1 === 4)) {
                $hasil2 .= $arrSkt[$i] . $arrSkt[$i + 1];
                $i      += 1;
            } elseif ($polaSekarang === 1) {
                $hasil2 .= $arrSkt[$i];
            } elseif ($polaSekarang === 2 && $polaSelanjutnya1 === 3 && ($polaSelanjutnya2 === 2 || $polaSelanjutnya2 === 4)) {
                $hasil2 .= $arrSkt[$i] . $arrSkt[$i + 1] . $arrSkt[$i + 2];
                $i      += 2;
            } elseif ($polaSekarang === 2 && $polaSelanjutnya1 === 3) {
                $hasil2 .= $arrSkt[$i] . $arrSkt[$i + 1];
                $i      += 1;
            } elseif ($polaSekarang === 3 && ($polaSelanjutnya1 === 2 || $polaSelanjutnya1 === 4)) {
                $hasil2 .= $arrSkt[$i] . $arrSkt[$i + 1];
                $i      += 1;
            } elseif ($polaSekarang === 3) {
                $hasil2 .= $arrSkt[$i];
            }

            $i++;
            $hasil2 .= "-";
        }

        // Membersihkan strip (-) yang berlebih di akhir
        return rtrim($hasil2, '-');
    }

    /**
     * Fungsi utama untuk memproses pemenggalan suku kata (Syllabification)
     */
    public function process(string $kata): string
    {
        // Reset state pola untuk setiap pemanggilan fungsi baru
        $this->pola = [];

        return $this->fsaTingkatSatu($kata);
    }
}