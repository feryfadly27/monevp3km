<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use Illuminate\Database\Seeder;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['id' => 1, 'nama' => 'Fakultas Teknik'],
            ['id' => 2, 'nama' => 'Jurusan Kebidanan'],
        ] as $fakultas) {
            Fakultas::updateOrCreate(
                ['id' => $fakultas['id']],
                ['nama' => $fakultas['nama']]
            );
        }
    }
}