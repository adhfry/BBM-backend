<?php
namespace Database\Seeders;

use App\Models\Affix;
use App\Models\Syllable;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LegacyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai ekstraksi data dari bbm_legacy...');

        // 1. Ekstrak Tabel 'admin' -> ke 'users'
        $oldAdmins = DB::connection('mysql_legacy')->table('admin')->get();
        foreach ($oldAdmins as $admin) {
            // Karena DB lama tidak punya email, kita buat dummy email dari username
            $dummyEmail = Str::slug($admin->username) . '@bbm.id';

            User::updateOrCreate(
                ['email' => $dummyEmail],
                [
                    'name'     => $admin->username,
                    'password' => Hash::make('12345678'), // Reset password seragam
                    'role'     => 'admin',
                ]
            );
        }
        $this->command->info('Migrasi Admin selesai.');

        // 2. Ekstrak Tabel 'suku_kata' -> ke 'syllables'
        $oldSyllables = DB::connection('mysql_legacy')->table('suku_kata')->get();
        foreach ($oldSyllables as $suku) {
            Syllable::firstOrCreate([
                'suku_kata' => trim($suku->skt),
            ]);
        }
        $this->command->info('Migrasi Suku Kata selesai.');

        // 3. Ekstrak Tabel 'imbuhan' -> ke 'affixes' (Pemisah ID & MD)
        // --- A. Ekstrak Tabel 'imbuhan_indo' ---
        $oldImbuhanIndo = DB::connection('mysql_legacy')->table('imbuhan_indo')->get();
        foreach ($oldImbuhanIndo as $indo) {
            $awalan  = ! empty(trim($indo->awalan)) ? trim($indo->awalan) : null;
            $akhiran = ! empty(trim($indo->akhiran)) ? trim($indo->akhiran) : null;

            // Menentukan letak berdasarkan isi awalan/akhiran
            $letak = 'awalan';
            if ($awalan && $akhiran) {
                $letak = 'awalan akhiran';
            } elseif (! $awalan && $akhiran) {
                $letak = 'akhiran';
            }

            if ($awalan || $akhiran) {
                Affix::create([
                    'bahasa'       => 'id',
                    'awalan'       => $awalan,
                    'akhiran'      => $akhiran,
                    'letak'        => $letak,
                    'arti_awalan'  => $indo->arti_awalan ?? null,  // Perbaikan di sini
                    'arti_akhiran' => $indo->arti_akhiran ?? null, // Perbaikan di sini
                ]);
            }
        }
        $this->command->info('Migrasi Imbuhan Indo selesai.');

        // --- B. Ekstrak Tabel 'imbuhan_madura' ---
        $oldImbuhanMadura = DB::connection('mysql_legacy')->table('imbuhan_madura')->get();
        foreach ($oldImbuhanMadura as $madura) {
            $awalan  = ! empty(trim($madura->awalan)) ? trim($madura->awalan) : null;
            $akhiran = ! empty(trim($madura->akhiran)) ? trim($madura->akhiran) : null;

            // Menentukan letak
            $letak = 'awalan';
            if ($awalan && $akhiran) {
                $letak = 'awalan akhiran';
            } elseif (! $awalan && $akhiran) {
                $letak = 'akhiran';
            }

            if ($awalan || $akhiran) {
                Affix::create([
                    'bahasa'       => 'md',
                    'awalan'       => $awalan,
                    'akhiran'      => $akhiran,
                    'letak'        => $letak,
                    'arti_awalan'  => $madura->arti_awalan ?? null,
                    'arti_akhiran' => $madura->arti_akhiran ?? null,
                ]);
            }
        }
        $this->command->info('Migrasi Imbuhan Madura selesai.');

        // 4. Ekstrak Tabel 'kamus' -> ke 'vocabularies'
        // Menggunakan order by 'id' sesuai struktur tabel asli
        DB::connection('mysql_legacy')->table('kamus')->orderBy('id')->chunk(500, function ($oldKamus) {
            $newVocabs = [];
            foreach ($oldKamus as $kata) {
                // Menggunakan kata_indo dan kata_madura sesuai nama field aslinya
                if (! empty(trim($kata->indonesia)) && ! empty(trim($kata->madura))) {
                    $newVocabs[] = [
                        'kata_indo'   => trim($kata->indonesia),
                        'kata_madura' => trim($kata->madura),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }

            // Insert data yang sudah bersih ke tabel baru
            if (count($newVocabs) > 0) {
                Vocabulary::insert($newVocabs);
            }
        });

        $this->command->info('Migrasi Kamus selesai.');
        $this->command->info('Semua data lama berhasil diintegrasikan ke struktur V2.0!');
    }
}
