<?php
namespace App\Services\Linguistics;

use App\Models\Vocabulary;

class MaduraStemmerService
{
    private string $terater  = '';
    private string $panoteng = '';

    /**
     * Mengecek apakah kata terdapat di tabel vocabularies (kolom kata_madura)
     */
    private function cekKamus(string $kata): bool
    {
        // Eloquent otomatis menangani SQL Injection, tidak perlu addslashes
        return Vocabulary::where('kata_madura', $kata)->exists();
    }

    /**
     * Mengecek kombinasi imbuhan terlarang
     */
    private function cekRulePrecedence(string $kata): bool
    {
        $pattern = "/(^è[^0-9]+(èpon|na)$)|(^a[^0-9]+èpon$)|(^ta[^0-9]+(è|wi|en|ana|aghi)$)|(^sa[^0-9]+(è|wi|nè)$)|(^pè[^0-9]+(è|wi|nè|en|wen)$)|(^pan[^0-9]+(è|wi|nè|en)$)|(^pam[^0-9]+(è|wi|nè|en)$)|(^pang[^0-9]+(è|wi|nè|en))|(^n[aiueo])|(^ny[aiueo])|(^m[aiueo])|(^ng[aiueo])/i";
        return preg_match($pattern, $kata) === 1;
    }

    /**
     * Menghapus possesive pronoun
     */
    private function hapusPossesivePronoun(string $kata, string &$akhiran): string
    {
        if (! $this->cekKamus($kata)) {
            if (preg_match("/(na|èpon|\Dèpon)$/i", $kata, $suffixes)) {
                $kata     = preg_replace("/(na|èpon|\Dèpon)$/i", "", $kata);
                $akhiran .= "-" . $suffixes[0];
            }
        }
        return $kata;
    }

    /**
     * Menghapus akhiran (derivational suffix)
     */
    private function hapusDerivationSuffixes(string $kata, string &$akhiran): string
    {
        if (! $this->cekKamus($kata)) {
            if (preg_match("/(e|è|wi|nè|â|a|wa|an|yan|wan|en|wen|na|ana|wana|yana|aghi|waghi|yaghi)$/i", $kata, $suffixes)) {
                $kata     = preg_replace("/(e|è|wi|nè|â|a|wa|an|yan|wan|en|wen|na|ana|wana|yana|aghi|waghi|yaghi)$/i", "", $kata);
                $akhiran .= "-" . $suffixes[0];

                // Jika huruf terakhir adalah huruf konsonan rangkap 2, maka hapus huruf terakhir
                if (preg_match("/[^aiueog]{2}$/i", $kata)) {
                    $kata = preg_replace("/[^aiueo]$/i", "", $kata);
                }
            }
        }
        return $kata;
    }

    /**
     * Menghapus awalan (derivational prefixes)
     */
    private function hapusDerivationPrefixes(string $kata, string &$awalan): string
    {
        $awalanprev = "";

        for ($i = 1; $i <= 3; $i++) {
            if (! $this->cekKamus($kata)) {
                if (preg_match("/^(e|è|a|ta|ma|ka|sa|pa|koma|kamè|kapè|pè|pan|pam|pang)/i", $kata, $prefixes)) {
                    if ($awalanprev !== $prefixes[0]) {
                        $kata        = preg_replace("/^(e|è|a|ta|ma|ka|sa|pa|koma|kamè|kapè|pè|pan|pam|pang)/i", "", $kata);
                        $awalan     .= $prefixes[0];
                        $awalanprev  = $prefixes[0];
                    }
                } elseif (preg_match("/^n[aiueo]/i", $kata, $prefixes)) {
                    if ($awalanprev !== $prefixes[0]) {
                        $kata        = preg_replace("/^n/i", "t", $kata);
                        $awalan     .= preg_replace("/[aiueo]/i", "", $prefixes[0]);
                        $awalanprev  = $prefixes[0];
                    }
                } elseif (preg_match("/^ny[aiueo]/i", $kata, $prefixes)) {
                    if ($awalanprev !== $prefixes[0]) {
                        $kata        = preg_replace("/^ny/i", "s", $kata);
                        $awalan     .= preg_replace("/[aiueo]/i", "", $prefixes[0]);
                        $awalanprev  = $prefixes[0];
                    }
                } elseif (preg_match("/^m[aiueo]/i", $kata, $prefixes)) {
                    if ($awalanprev !== $prefixes[0]) {
                        $kata        = preg_replace("/^m/i", "p", $kata);
                        $awalan     .= preg_replace("/[aiueo]/i", "", $prefixes[0]);
                        $awalanprev  = $prefixes[0];
                    }
                } elseif (preg_match("/^ng[aiueo]/i", $kata, $prefixes)) {
                    if ($awalanprev !== $prefixes[0]) {
                        $kata    = preg_replace("/^ng/i", "k", $kata);
                        $awalan .= preg_replace("/[aiueo]/i", "", $prefixes[0]);

                        if (! $this->cekKamus($kata)) {
                            $kata = preg_replace("/^k/i", "", $kata);
                        }
                        $awalanprev = $prefixes[0];
                    }
                }
            } else {
                $i = 4; // Hentikan perulangan jika ada di kamus
            }
        }
        return $kata;
    }

    /**
     * Menghapus sisipan
     */
    private function hapusSisipan(string $kata): string
    {
        $kataAwal = $kata;
        if (! $this->cekKamus($kata)) {
            if (preg_match("/^[^0-9]+(al|ar|en|in|om|um|am)/i", $kata, $sisipan)) {
                $kata = preg_replace("/(al|ar|um|en|in|om|am)/i", "", $kata, 1);
            }
        }

        if ($this->cekKamus($kata)) {
            return $kata;
        }
        return $kataAwal;
    }

    /**
     * Menghapus awalan khusus untuk loop_pengembalian_akhiran
     */
    private function hapusAwalan(string $kata): string
    {
        $awalan     = "";
        $awalanprev = "";

        if (! $this->cekKamus($kata)) {
            if (preg_match("/^(e|è|a|ta|ma|ka|sa|pa|koma|kamè|kapè|pè|pan|pam|pang)/i", $kata, $prefixes)) {
                if ($awalanprev !== $prefixes[0]) {
                    $kata        = preg_replace("/^(e|è|a|ta|ma|ka|sa|pa|koma|kamè|kapè|pè|pan|pam|pang)/i", "", $kata);
                    $awalan     .= $prefixes[0];
                    $awalanprev  = $prefixes[0];
                }
            } elseif (preg_match("/^n[aiueo]/i", $kata, $prefixes)) {
                if ($awalanprev !== $prefixes[0]) {
                    $kata        = preg_replace("/^n/i", "t", $kata);
                    $awalan     .= preg_replace("/[aiueo]/i", "", $prefixes[0]);
                    $awalanprev  = $prefixes[0];
                }
            } elseif (preg_match("/^ny[aiueo]/i", $kata, $prefixes)) {
                if ($awalanprev !== $prefixes[0]) {
                    $kata        = preg_replace("/^ny/i", "s", $kata);
                    $awalan     .= preg_replace("/[aiueo]/i", "", $prefixes[0]);
                    $awalanprev  = $prefixes[0];
                }
            } elseif (preg_match("/^m[aiueo]/i", $kata, $prefixes)) {
                if ($awalanprev !== $prefixes[0]) {
                    $kata        = preg_replace("/^m/i", "p", $kata);
                    $awalan     .= preg_replace("/[aiueo]/i", "", $prefixes[0]);
                    $awalanprev  = $prefixes[0];
                }
            } elseif (preg_match("/^ng[aiueo]/i", $kata, $prefixes)) {
                if ($awalanprev !== $prefixes[0]) {
                    $kata        = preg_replace("/^ng/i", "k", $kata);
                    $awalan     .= preg_replace("/[aiueo]/i", "", $prefixes[0]);
                    $awalanprev  = $prefixes[0];
                }
            }
        }
        return $kata;
    }

    /**
     * Mengembalikan akhiran jika kata yang dipenggal tidak ditemukan
     */
    private function loopPengembalianAkhiran(string $kata, string $prefix, string $suffix): string
    {
        $akhiran    = explode("-", $suffix);
        $prefixTrim = trim($prefix);

        switch ($prefixTrim) {
            case "n":$kata = preg_replace("/^t/i", "n", $kata);
                break;
            case "ny":$kata = preg_replace("/^s/i", "ny", $kata);
                break;
            case "m":$kata = preg_replace("/^p/i", "m", $kata);
                break;
            case "ng":$kata = preg_replace("/^k/i", "ng", $kata);
                break;
            default: $kata = $prefix . $kata;
        }

        $kata = $this->hapusAwalan($kata);

        // Looping pengembalian
        for ($i = 1; $i < count($akhiran); $i++) {
            if ($this->cekKamus($kata)) {
                break;
            } else {
                if (! empty($akhiran[$i])) {
                    $kata = $kata . $akhiran[$i];
                    $kata = $this->hapusAwalan($kata);
                }
            }
        }

        if (! $this->cekKamus($kata)) {
            switch ($prefixTrim) {
                case "n":$kata = preg_replace("/^t/i", "n", $kata);
                    break;
                case "ny":$kata = preg_replace("/^s/i", "ny", $kata);
                    break;
                case "m":$kata = preg_replace("/^p/i", "m", $kata);
                    break;
                case "ng":$kata = preg_replace("/^k/i", "ng", $kata);
                    break;
                default: $kata = $prefix . $kata;
            }
        }
        return $kata;
    }

    /**
     * Fungsi utama Stemming bahasa Madura
     */
    public function process(string $kata): array
    {
        $kata     = trim($kata);
        $kataAwal = $kata;
        $dp       = ''; // Awalan
        $ds       = ''; // Akhiran
        $pp       = ''; // Pronoun

        if (preg_match("/\-/", $kata)) {
            $kataArr = explode("-", $kata);

            if ($this->cekRulePrecedence($kataArr[1])) {
                $kataArr[0] = $this->hapusSisipan($this->hapusDerivationSuffixes($this->hapusPossesivePronoun($this->hapusDerivationPrefixes($kataArr[0], $dp), $pp), $ds));
                $kataArr[1] = $this->hapusSisipan($this->hapusDerivationSuffixes($this->hapusPossesivePronoun($this->hapusDerivationPrefixes($kataArr[1], $dp), $pp), $ds));
                $kata       = $kataArr[1];
            } else {
                $kataArr[0] = $this->hapusSisipan($this->hapusDerivationPrefixes($this->hapusDerivationSuffixes($this->hapusPossesivePronoun($kataArr[0], $pp), $ds), $dp));
                $kataArr[1] = $this->hapusSisipan($this->hapusDerivationPrefixes($this->hapusDerivationSuffixes($this->hapusPossesivePronoun($kataArr[1], $pp), $ds), $dp));
                $kata       = $kataArr[1];
            }
        } else {
            if ($this->cekRulePrecedence($kata)) {
                $kata = $this->hapusSisipan($this->hapusDerivationSuffixes($this->hapusPossesivePronoun($this->hapusDerivationPrefixes($kata, $dp), $pp), $ds));
            } else {
                $kata = $this->hapusSisipan($this->hapusDerivationPrefixes($this->hapusDerivationSuffixes($this->hapusPossesivePronoun($kata, $pp), $ds), $dp));
            }
        }

        if (! $this->cekKamus($kata)) {
            $kata = $this->loopPengembalianAkhiran($kata, $dp, "$ds$pp");
        }

        if (! $this->cekKamus($kata)) {
            $kata = $kataAwal;
        }

        $this->terater  = $dp;
        $this->panoteng = $ds . $pp;

        return [
            'kata_dasar' => $kata,
            'awalan'     => $this->terater,
            'akhiran'    => $this->panoteng,
        ];
    }
}