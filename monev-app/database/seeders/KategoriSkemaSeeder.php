<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSkemaSeeder extends Seeder
{
    public function run(): void
    {
        $penelitian = DB::table('kategori')->insertGetId(['nama' => 'Penelitian', 'kode' => 'PENELITIAN', 'created_at' => now(), 'updated_at' => now()]);
        $pengmas    = DB::table('kategori')->insertGetId(['nama' => 'Pengabdian Masyarakat', 'kode' => 'PENGMAS', 'created_at' => now(), 'updated_at' => now()]);

        $skema = [
            [$penelitian, 'PD',  'Penelitian Dasar',        50000000],
            [$penelitian, 'PT',  'Penelitian Terapan',       75000000],
            [$penelitian, 'PDP', 'Penelitian Dosen Pemula',  20000000],
            [$pengmas,    'PKM', 'PKM Kemitraan Masyarakat', 35000000],
            [$pengmas,    'PBP', 'PKM Berbasis Produk',      30000000],
            [$pengmas,    'PDP-PM', 'PKM Dosen Pemula',      15000000],
        ];

        foreach ($skema as [$kat, $kode, $nama, $dana]) {
            DB::table('skema')->insert(['kategori_id' => $kat, 'kode' => $kode, 'nama' => $nama, 'dana_maksimal' => $dana, 'durasi_bulan' => 12, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
