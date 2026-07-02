<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['id' => 1, 'fakultas_id' => 1, 'nama' => 'Teknik Informatika'],
            ['id' => 2, 'fakultas_id' => 2, 'nama' => 'Prodi D3 Kebidanan'],
        ] as $prodi) {
            Prodi::updateOrCreate(
                ['id' => $prodi['id']],
                ['fakultas_id' => $prodi['fakultas_id'], 'nama' => $prodi['nama']]
            );
        }
    }
}