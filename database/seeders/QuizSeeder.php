<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Section;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $quizzes = [
            // Section 1 - Pengenalan Dasar
            1 => [
                [
                    'question'       => 'Apa yang dimaksud dengan LSM?',
                    'option_a'       => 'Lembaga Sosial Masyarakat',
                    'option_b'       => 'Lembaga Swadaya Masyarakat',
                    'option_c'       => 'Lembaga Swasta Mandiri',
                    'option_d'       => 'Lembaga Sosial Mandiri',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Apa tujuan utama berdirinya LSM?',
                    'option_a'       => 'Mencari keuntungan finansial',
                    'option_b'       => 'Mendukung kepentingan pemerintah saja',
                    'option_c'       => 'Melayani kepentingan masyarakat secara independen',
                    'option_d'       => 'Menjadi pesaing perusahaan swasta',
                    'correct_answer' => 'c',
                ],
                [
                    'question'       => 'Sumber pendanaan LSM umumnya berasal dari?',
                    'option_a'       => 'APBN pemerintah saja',
                    'option_b'       => 'Donasi, hibah, dan program kemitraan',
                    'option_c'       => 'Penjualan saham di bursa efek',
                    'option_d'       => 'Pinjaman bank komersial',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Manakah yang BUKAN karakteristik LSM?',
                    'option_a'       => 'Nirlaba',
                    'option_b'       => 'Independen dari pemerintah',
                    'option_c'       => 'Berorientasi profit tinggi',
                    'option_d'       => 'Berbasis komunitas',
                    'correct_answer' => 'c',
                ],
            ],

            // Section 2 - Struktur Organisasi
            2 => [
                [
                    'question'       => 'Apa peran Dewan Pengawas dalam LSM?',
                    'option_a'       => 'Menjalankan program harian',
                    'option_b'       => 'Mengawasi jalannya organisasi agar sesuai visi misi',
                    'option_c'       => 'Mencari donatur baru',
                    'option_d'       => 'Membuat konten media sosial',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Divisi mana yang bertanggung jawab atas laporan keuangan?',
                    'option_a'       => 'Divisi Program',
                    'option_b'       => 'Divisi Humas',
                    'option_c'       => 'Divisi Keuangan',
                    'option_d'       => 'Divisi Advokasi',
                    'correct_answer' => 'c',
                ],
                [
                    'question'       => 'Apa yang dimaksud dengan struktur flat dalam organisasi?',
                    'option_a'       => 'Struktur dengan banyak level hierarki',
                    'option_b'       => 'Struktur dengan sedikit level hierarki, komunikasi lebih langsung',
                    'option_c'       => 'Struktur yang hanya memiliki satu orang pemimpin',
                    'option_d'       => 'Struktur tanpa pembagian tugas',
                    'correct_answer' => 'b',
                ],
            ],

            // Section 3 - Program dan Kegiatan
            3 => [
                [
                    'question'       => 'Apa yang dimaksud dengan program pemberdayaan masyarakat?',
                    'option_a'       => 'Program yang hanya memberikan bantuan tunai',
                    'option_b'       => 'Program yang meningkatkan kapasitas dan kemandirian masyarakat',
                    'option_c'       => 'Program hiburan untuk masyarakat',
                    'option_d'       => 'Program rekrutmen anggota baru',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Advokasi kebijakan dalam konteks LSM berarti?',
                    'option_a'       => 'Memberikan bantuan hukum gratis',
                    'option_b'       => 'Memperjuangkan perubahan kebijakan yang berpihak pada masyarakat',
                    'option_c'       => 'Mendukung semua kebijakan pemerintah',
                    'option_d'       => 'Menolak semua regulasi yang ada',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Monitoring program dilakukan untuk tujuan?',
                    'option_a'       => 'Mencari kesalahan pelaksana program',
                    'option_b'       => 'Memastikan program berjalan sesuai rencana dan tujuan',
                    'option_c'       => 'Membuat laporan tahunan saja',
                    'option_d'       => 'Memenuhi syarat administrasi donor',
                    'correct_answer' => 'b',
                ],
            ],

            // Section 4 - Manajemen Keuangan
            4 => [
                [
                    'question'       => 'Apa yang dimaksud akuntabilitas dalam pengelolaan keuangan LSM?',
                    'option_a'       => 'Menyembunyikan informasi keuangan',
                    'option_b'       => 'Kewajiban untuk mempertanggungjawabkan penggunaan dana kepada pemangku kepentingan',
                    'option_c'       => 'Hanya membuat laporan internal',
                    'option_d'       => 'Menggunakan dana sesuka hati',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Apa fungsi audit eksternal bagi LSM?',
                    'option_a'       => 'Memeriksa kondisi gedung kantor',
                    'option_b'       => 'Menilai kewajaran laporan keuangan secara independen',
                    'option_c'       => 'Menentukan gaji karyawan',
                    'option_d'       => 'Mencari sumber donasi baru',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Prinsip transparansi keuangan mengharuskan LSM untuk?',
                    'option_a'       => 'Merahasiakan semua transaksi',
                    'option_b'       => 'Membuka informasi keuangan kepada publik dan pemangku kepentingan',
                    'option_c'       => 'Hanya melaporkan ke pemerintah',
                    'option_d'       => 'Tidak membuat laporan keuangan',
                    'correct_answer' => 'b',
                ],
            ],

            // Section 5 - Evaluasi dan Pelaporan
            5 => [
                [
                    'question'       => 'Apa perbedaan monitoring dan evaluasi?',
                    'option_a'       => 'Keduanya sama persis',
                    'option_b'       => 'Monitoring dilakukan selama program berjalan, evaluasi dilakukan di akhir untuk mengukur dampak',
                    'option_c'       => 'Evaluasi lebih mudah dari monitoring',
                    'option_d'       => 'Monitoring hanya dilakukan oleh donor',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Apa komponen utama dalam laporan kegiatan LSM?',
                    'option_a'       => 'Hanya foto kegiatan',
                    'option_b'       => 'Latar belakang, tujuan, pelaksanaan, hasil, dan rekomendasi',
                    'option_c'       => 'Hanya data keuangan',
                    'option_d'       => 'Daftar nama peserta saja',
                    'correct_answer' => 'b',
                ],
                [
                    'question'       => 'Indikator keberhasilan program digunakan untuk?',
                    'option_a'       => 'Membandingkan dengan organisasi lain',
                    'option_b'       => 'Mengukur sejauh mana tujuan program tercapai secara terukur',
                    'option_c'       => 'Menentukan jumlah karyawan',
                    'option_d'       => 'Menarik perhatian media',
                    'correct_answer' => 'b',
                ],
            ],
        ];

        foreach ($quizzes as $sectionOrder => $questions) {
            $section = Section::where('order', $sectionOrder)->first();
            if (!$section) continue;

            foreach ($questions as $q) {
                Quiz::updateOrCreate(
                    [
                        'section_id' => $section->id,
                        'question'   => $q['question'],
                    ],
                    array_merge($q, ['section_id' => $section->id])
                );
            }
        }
    }
}
