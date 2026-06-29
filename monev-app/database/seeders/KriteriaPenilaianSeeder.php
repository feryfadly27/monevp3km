<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        $kriteria = [
            ['Kesesuaian pelaksanaan dengan proposal', 25.00, 1],
            ['Capaian luaran / target skema',          30.00, 2],
            ['Kemajuan & ketepatan waktu',             20.00, 3],
            ['Penggunaan dana / kewajaran',            15.00, 4],
            ['Kualitas laporan & bukti',               10.00, 5],
        ];

        foreach ($kriteria as [$nama, $bobot, $urutan]) {
            DB::table('kriteria_penilaian')->insert([
                'scope'   => 'GLOBAL',
                'nama'    => $nama,
                'bobot'   => $bobot,
                'skor_min' => 1,
                'skor_max' => 100,
                'urutan'  => $urutan,
                'aktif'   => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
