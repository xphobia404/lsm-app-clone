<?php

namespace Database\Seeders;

use App\Models\LearningSchema;
use Illuminate\Database\Seeder;

class LearningSchemaSeeder extends Seeder
{
    public function run(): void
    {
        $schemas = [
            [
                'title'       => 'Dasar-Dasar Akuntansi',
                'description' => 'Materi pengenalan akuntansi mulai dari konsep dasar, persamaan akuntansi, hingga laporan keuangan sederhana.',
                'is_active'   => true,
            ],
            [
                'title'       => 'Manajemen Keuangan',
                'description' => 'Mempelajari pengelolaan keuangan perusahaan, analisis rasio, dan pengambilan keputusan investasi.',
                'is_active'   => true,
            ],
            [
                'title'       => 'Perpajakan Indonesia',
                'description' => 'Panduan lengkap perpajakan Indonesia meliputi PPh, PPN, dan tata cara pelaporan pajak.',
                'is_active'   => true,
            ],
            [
                'title'       => 'Audit & Pemeriksaan Keuangan',
                'description' => 'Prinsip dan prosedur audit internal maupun eksternal sesuai standar yang berlaku.',
                'is_active'   => false, // untuk testing status non-aktif
            ],
        ];

        foreach ($schemas as $schema) {
            LearningSchema::create($schema);
        }
    }
}
