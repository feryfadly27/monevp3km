<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSkemaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori')->updateOrInsert(
            ['kode' => 'PENELITIAN'],
            ['nama' => 'Penelitian', 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('kategori')->updateOrInsert(
            ['kode' => 'PENGMAS'],
            ['nama' => 'Pengabdian Masyarakat', 'updated_at' => now(), 'created_at' => now()]
        );

        $penelitian = DB::table('kategori')->where('kode', 'PENELITIAN')->value('id');
        $pengmas    = DB::table('kategori')->where('kode', 'PENGMAS')->value('id');

        $skema = [
            [$penelitian, 'PD',  'Penelitian Dasar',        50000000],
            [$penelitian, 'PT',  'Penelitian Terapan',       75000000],
            [$penelitian, 'PDP', 'Penelitian Dosen Pemula',  20000000],
            [$pengmas,    'PKM', 'PKM Kemitraan Masyarakat', 35000000],
            [$pengmas,    'PBP', 'PKM Berbasis Produk',      30000000],
            [$pengmas,    'PDP-PM', 'PKM Dosen Pemula',      15000000],
        ];

        foreach ($skema as [$kat, $kode, $nama, $dana]) {
            DB::table('skema')->updateOrInsert(
                ['kode' => $kode],
                [
                    'kategori_id' => $kat,
                    'nama' => $nama,
                    'dana_maksimal' => $dana,
                    'durasi_bulan' => 12,
                    'aktif' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
