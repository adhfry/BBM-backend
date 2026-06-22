<?php
namespace App\Services\Linguistics;

use App\Models\Vocabulary;

class IndoStemmerService
{
/**
 * Mengecek apakah kata terdapat di tabel vocabularies (kolom kata_indo)
 */
    private function cekKamus(string $kata): bool
    {
        return Vocabulary::where('kata_indo', $kata)->exists();
    }

/**
 * Hapus Inflection Suffixes ("-lah", "-kah", "-ku", "-mu", atau "-nya")
 */
    private function delInflectionSuffixes(string $kata): string
    {
        $kataAsal = $kata;
// Mengganti eregi dengan preg_match dengan modifier /i
        if (preg_match('/([km]u|nya|[kl]ah|pun)$/i', $kata)) {
            $kataBaru = preg_replace('/([km]u|nya|[kl]ah|pun)$/i', '', $kata);

// Jika berupa particles ("-lah", "-kah", "-tah" atau "-pun")
            if (preg_match('/([klt]ah|pun)$/i', $kata)) {
// Hapus Possesive Pronouns ("-ku", "-mu", atau "-nya")
                if (preg_match('/([km]u|nya)$/i', $kataBaru)) {
                    return preg_replace('/([km]u|nya)$/i', '', $kataBaru);
                }
            }
            return $kataBaru;
        }
        return $kataAsal;
    }

/**
 * Cek Rule Precedence (Kombinasi Awalan dan Akhiran)
 */
    private function cekRulePrecedence(string $kata): bool
    {
        if (preg_match('/^(be)[a-z]+(lah|an)$/i', $kata)) {
            return true;
        }

        if (preg_match('/^(di|([mpt]e))[a-z]+(i)$/i', $kata)) {
            return true;
        }

        return false;
    }

/**
 * Cek Prefix Disallowed Sufixes
 */
    private function cekPrefixDisallowedSufixes(string $kata): bool
    {
        if (preg_match('/^(be)[a-z]+(i)$/i', $kata)) {
            return true;
        }

        if (preg_match('/^(di)[a-z]+(an)$/i', $kata)) {
            return true;
        }

        if (preg_match('/^(ke)[a-z]+(i|kan)$/i', $kata)) {
            return true;
        }

        if (preg_match('/^(me)[a-z]+(an)$/i', $kata)) {
            return true;
        }

        if (preg_match('/^(se)[a-z]+(i|kan)$/i', $kata)) {
            return true;
        }

        return false;
    }

/**
 * Hapus Derivation Suffixes ("-i", "-an" atau "-kan")
 */
    private function delDerivationSuffixes(string $kata): string
    {
        $kataAsal = $kata;
        if (preg_match('/(kan)$/i', $kata)) {
            $kataBaru = preg_replace('/(kan)$/i', '', $kata);
            if ($this->cekKamus($kataBaru)) {
                return $kataBaru;
            }

        }
        if (preg_match('/(an|i)$/i', $kata)) {
            $kataBaru = preg_replace('/(an|i)$/i', '', $kata);
            if ($this->cekKamus($kataBaru)) {
                return $kataBaru;
            }

        }
        return $kataAsal;
    }

/**
 * Hapus Derivation Prefix ("di-", "ke-", "se-", "te-", "be-", "me-", atau "pe-")
 */
    private function delDerivationPrefix(string $kata): string
    {
        $kataAsal = $kata;

        if (preg_match('/^(di|[ks]e)\S{1,}/i', $kata)) {
            $kataBaru = preg_replace('/^(di|[ks]e)/i', '', $kata);
            if ($this->cekKamus($kataBaru)) {
                return $kataBaru;
            }

            $kataBaru2 = $this->delDerivationSuffixes($kataBaru);
            if ($this->cekKamus($kataBaru2)) {
                return $kataBaru2;
            }

        }

        if (preg_match('/^([tmbp]e)\S{1,}/i', $kata)) {
// Awalan 'be-'
            if (preg_match('/^(be)\S{1,}/i', $kata)) {
                if (preg_match('/^(ber)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(ber)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(ber)/i', 'r', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(ber)[^aiueor][a-z](?!er)\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(ber)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(ber)[^aiueor][a-z]er[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(ber)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^belajar\S{0,}/i', $kata)) {
                    $k = preg_replace('/^(bel)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(be)[^aiueolr]er[^aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(be)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
            }

// Awalan 'te-'
            if (preg_match('/^(te)\S{1,}/i', $kata)) {
                if (preg_match('/^(terr)\S{1,}/i', $kata)) {
                    return $kata;
                }

                if (preg_match('/^(ter)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(ter)/i', 'r', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(ter)[^aiueor]er[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(ter)[^aiueor](?!er)\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(te)[^aiueor]er[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(te)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(ter)[^aiueor]er[^aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
            }

// Awalan 'me-'
            if (preg_match('/^(me)\S{1,}/i', $kata)) {
                if (preg_match('/^(me)[lrwyv][aiueo]/i', $kata)) {
                    $k = preg_replace('/^(me)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(mem)[bfvp]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(mem)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(mem)((r[aiueo])|[aiueo])\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(mem)/i', 'm', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(mem)/i', 'p', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(men)[cdjszt]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(men)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(men)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(men)/i', 'n', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(men)/i', 't', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(meng)[ghqk]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(meng)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(meng)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(meng)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(meng)/i', 'k', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(menge)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(meny)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(meny)/i', 's', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(me)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
            }

// Awalan 'pe-'
            if (preg_match('/^(pe)\S{1,}/i', $kata)) {
                if (preg_match('/^(pe)[wy]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pe)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(per)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(per)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(per)/i', 'r', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(per)[^aiueor][a-z](?!er)\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(per)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(per)[^aiueor][a-z](er)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(per)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pem)[bfv]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pem)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pem)(r[aiueo]|[aiueo])\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pem)/i', 'm', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(pem)/i', 'p', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pen)[cdjzt]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pen)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pen)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pen)/i', 'n', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(pen)/i', 't', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(peng)[^aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(peng)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(peng)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(peng)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(peng)/i', 'k', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(penge)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(peny)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(peny)/i', 's', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                    $k = preg_replace('/^(pe)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pel)[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pel)/i', 'l', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pelajar)\S{0,}/i', $kata)) {
                    $k = preg_replace('/^(pel)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pe)[^rwylmn]er[aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pe)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pe)[^rwylmn](?!er)\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pe)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
                if (preg_match('/^(pe)[^aiueor]er[^aiueo]\S{1,}/i', $kata)) {
                    $k = preg_replace('/^(pe)/i', '', $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
            }
        }

// Awalan-awalan gabungan khusus
        $awalanGabungan = ['memper', 'mempel', 'menter', 'member', 'diper', 'diter', 'dipel', 'diber', 'keber', 'keter',
            'berke'];
        foreach ($awalanGabungan as $awalan) {
            if (preg_match("/^($awalan)\S{1,}/i", $kata)) {
                $k = preg_replace("/^($awalan)/i", '', $kata);
                if ($this->cekKamus($k)) {
                    return $k;
                }

                $k2 = $this->delDerivationSuffixes($k);
                if ($this->cekKamus($k2)) {
                    return $k2;
                }

// Cek luluh r atau l (kecuali berke)
                if ($awalan !== 'berke') {
                    $hurufLuluh = in_array($awalan, ['mempel', 'dipel']) ? 'l' : 'r';
                    $k          = preg_replace("/^($awalan)/i", $hurufLuluh, $kata);
                    if ($this->cekKamus($k)) {
                        return $k;
                    }

                    $k2 = $this->delDerivationSuffixes($k);
                    if ($this->cekKamus($k2)) {
                        return $k2;
                    }

                }
            }
        }

        if (! preg_match('/^(di|[kstbmp]e)\S{1,}/i', $kata)) {
            return $kataAsal;
        }

        return $kataAsal;
    }

/**
 * Fungsi utama stemming (Enhanced Confix Stripping)
 */
    public function process(string $kata): string
    {
        $kata = trim(strtolower($kata));

// 1. Cek Kata di Kamus
        if ($this->cekKamus($kata)) {
            return $kata;
        }

// 2. Buang Inflection suffixes ("-lah", "-kah", "-ku", "-mu", atau "-nya")
        $kata = $this->delInflectionSuffixes($kata);

// 3. Buang Derivation suffix ("-i" or "-an" atau "-kan")
        $kata = $this->delDerivationSuffixes($kata);

// 4. Buang Derivation prefix
        $kata = $this->delDerivationPrefix($kata);

        return $kata;
    }
}