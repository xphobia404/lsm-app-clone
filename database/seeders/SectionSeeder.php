<?php

namespace Database\Seeders;

use App\Models\LearningSchema;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            // ── Akuntansi Dasar ───────────────────────────────────────
            [
                'title'       => 'Pengenalan Akuntansi',
                'description' => 'Sejarah, definisi, dan peran akuntansi dalam bisnis modern.',
                'is_active'   => true,
                'schemas'     => ['Dasar-Dasar Akuntansi'],
                'order'       => [1],
            ],
            [
                'title'       => 'Persamaan Dasar Akuntansi',
                'description' => 'Memahami konsep Aset = Liabilitas + Ekuitas beserta contoh transaksinya.',
                'is_active'   => true,
                'schemas'     => ['Dasar-Dasar Akuntansi'],
                'order'       => [2],
            ],
            [
                'title'       => 'Jurnal & Buku Besar',
                'description' => 'Cara mencatat transaksi ke jurnal umum dan memindahkannya ke buku besar.',
                'is_active'   => true,
                'schemas'     => ['Dasar-Dasar Akuntansi', 'Audit & Pemeriksaan Keuangan'],
                'order'       => [3, 2],
            ],
            [
                'title'       => 'Laporan Keuangan Dasar',
                'description' => 'Menyusun neraca, laporan laba rugi, dan laporan arus kas sederhana.',
                'is_active'   => true,
                'schemas'     => ['Dasar-Dasar Akuntansi', 'Manajemen Keuangan'],
                'order'       => [4, 1],
            ],
            // ── Manajemen Keuangan ────────────────────────────────────
            [
                'title'       => 'Analisis Rasio Keuangan',
                'description' => 'Menggunakan rasio likuiditas, solvabilitas, dan profitabilitas untuk menilai kinerja perusahaan.',
                'is_active'   => true,
                'schemas'     => ['Manajemen Keuangan'],
                'order'       => [2],
            ],
            [
                'title'       => 'Manajemen Modal Kerja',
                'description' => 'Pengelolaan aset lancar dan kewajiban jangka pendek secara efisien.',
                'is_active'   => true,
                'schemas'     => ['Manajemen Keuangan'],
                'order'       => [3],
            ],
            [
                'title'       => 'Keputusan Investasi & Pendanaan',
                'description' => 'NPV, IRR, dan struktur modal yang optimal.',
                'is_active'   => true,
                'schemas'     => ['Manajemen Keuangan'],
                'order'       => [4],
            ],
            // ── Perpajakan ────────────────────────────────────────────
            [
                'title'       => 'Pengantar Perpajakan',
                'description' => 'Sistem perpajakan Indonesia, jenis pajak, dan subjek pajak.',
                'is_active'   => true,
                'schemas'     => ['Perpajakan Indonesia'],
                'order'       => [1],
            ],
            [
                'title'       => 'Pajak Penghasilan (PPh)',
                'description' => 'PPh Pasal 21, 22, 23, 25, dan 29 beserta cara perhitungannya.',
                'is_active'   => true,
                'schemas'     => ['Perpajakan Indonesia'],
                'order'       => [2],
            ],
            [
                'title'       => 'Pajak Pertambahan Nilai (PPN)',
                'description' => 'Mekanisme PPN, faktur pajak, dan pelaporan SPT Masa PPN.',
                'is_active'   => true,
                'schemas'     => ['Perpajakan Indonesia'],
                'order'       => [3],
            ],
            // ── Audit ─────────────────────────────────────────────────
            [
                'title'       => 'Standar & Etika Audit',
                'description' => 'Standar Profesional Akuntan Publik (SPAP) dan kode etik profesi.',
                'is_active'   => true,
                'schemas'     => ['Audit & Pemeriksaan Keuangan'],
                'order'       => [1],
            ],
            [
                'title'       => 'Prosedur Audit',
                'description' => 'Perencanaan, pelaksanaan, dan pelaporan hasil audit.',
                'is_active'   => false, // section non-aktif untuk testing
                'schemas'     => ['Audit & Pemeriksaan Keuangan'],
                'order'       => [3],
            ],
        ];

        $schemaCache = LearningSchema::pluck('id', 'title');

        foreach ($sections as $data) {
            $section = Section::create([
                'title'       => $data['title'],
                'description' => $data['description'],
                'is_active'   => $data['is_active'],
            ]);

            // Attach ke learning schemas
            $pivotData = [];
            foreach ($data['schemas'] as $i => $schemaTitle) {
                if ($schemaId = $schemaCache[$schemaTitle] ?? null) {
                    $pivotData[$schemaId] = ['section_order' => $data['order'][$i]];
                }
            }
            if ($pivotData) {
                $section->learningSchemas()->attach($pivotData);
            }
        }
    }
}
