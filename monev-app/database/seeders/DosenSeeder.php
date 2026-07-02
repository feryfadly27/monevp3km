<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['id' => 1, 'prodi_id' => 1, 'nidn' => '0001018001', 'nama' => 'Prof. Dr. Suharto, M.T', 'email' => 'suharto@univ.ac.id', 'no_hp' => null],
            ['id' => 2, 'prodi_id' => 1, 'nidn' => '0002028002', 'nama' => 'Dr. Hendra Setiawan, M.Kom', 'email' => 'hendra@univ.ac.id', 'no_hp' => null],
            ['id' => 3, 'prodi_id' => 1, 'nidn' => '0003038003', 'nama' => 'Dewi Anggraini, M.Si', 'email' => 'dewi@univ.ac.id', 'no_hp' => null],
            ['id' => 4, 'prodi_id' => 1, 'nidn' => '0004048004', 'nama' => 'Dr. Rini Kusumawati', 'email' => 'rini@univ.ac.id', 'no_hp' => null],
            ['id' => 5, 'prodi_id' => 1, 'nidn' => '0005058005', 'nama' => 'Andi Pratama, S.Pd', 'email' => 'andi@univ.ac.id', 'no_hp' => null],
            ['id' => 6, 'prodi_id' => 1, 'nidn' => '4027099001', 'nama' => 'Fery Fadly', 'email' => 'ferfadl27@gmail.com', 'no_hp' => '085366525565'],
        ] as $dosen) {
            Dosen::updateOrCreate(
                ['id' => $dosen['id']],
                [
                    'user_id' => null,
                    'prodi_id' => $dosen['prodi_id'],
                    'nidn' => $dosen['nidn'],
                    'nama' => $dosen['nama'],
                    'email' => $dosen['email'],
                    'no_hp' => $dosen['no_hp'],
                ]
            );
        }
    }
}