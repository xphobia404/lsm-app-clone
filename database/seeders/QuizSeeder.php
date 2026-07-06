<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Section;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $quizMap = [
            'Pengenalan Akuntansi' => [
                [
                    'question'       => 'Siapakah yang pertama kali mendokumentasikan sistem pembukuan berpasangan (double-entry bookkeeping)?',
                    'option_a'       => 'Adam Smith',
                    'option_b'       => 'Luca Pacioli',
                    'option_c'       => 'John Maynard Keynes',
                    'option_d'       => 'Benjamin Graham',
                    'correct_answer' => 'b',
                    'explanation'    => 'Luca Pacioli mendokumentasikannya dalam buku Summa de Arithmetica pada tahun 1494.',
                    'quiz_order'     => 1,
                ],
                [
                    'question'       => 'Akuntansi sering disebut sebagai...',
                    'option_a'       => 'Bahasa Hukum',
                    'option_b'       => 'Bahasa Ilmu Sosial',
                    'option_c'       => 'Bahasa Bisnis',
                    'option_d'       => 'Bahasa Matematika',
                    'correct_answer' => 'c',
                    'explanation'    => 'Akuntansi disebut bahasa bisnis karena laporan keuangan mengkomunikasikan kondisi keuangan kepada berbagai pihak.',
                    'quiz_order'     => 2,
                ],
            ],
            'Persamaan Dasar Akuntansi' => [
                [
                    'question'       => 'Manakah persamaan dasar akuntansi yang benar?',
                    'option_a'       => 'Aset = Pendapatan - Beban',
                    'option_b'       => 'Aset = Liabilitas + Ekuitas',
                    'option_c'       => 'Aset = Ekuitas - Liabilitas',
                    'option_d'       => 'Aset + Liabilitas = Ekuitas',
                    'correct_answer' => 'b',
                    'explanation'    => 'Persamaan dasar akuntansi: Aset = Liabilitas + Ekuitas. Ini adalah fondasi seluruh sistem pencatatan akuntansi.',
                    'quiz_order'     => 1,
                ],
                [
                    'question'       => 'Jika pemilik menyetorkan modal Rp 10.000.000, dampaknya adalah...',
                    'option_a'       => 'Kas bertambah, Liabilitas bertambah',
                    'option_b'       => 'Kas berkurang, Ekuitas bertambah',
                    'option_c'       => 'Kas bertambah, Ekuitas bertambah',
                    'option_d'       => 'Tidak ada perubahan',
                    'correct_answer' => 'c',
                    'explanation'    => 'Setoran modal meningkatkan Kas (Aset) sekaligus meningkatkan Modal (Ekuitas) dalam jumlah yang sama.',
                    'quiz_order'     => 2,
                ],
            ],
            'Jurnal & Buku Besar' => [
                [
                    'question'       => 'Proses memindahkan data dari jurnal ke buku besar disebut...',
                    'option_a'       => 'Penjurnalan',
                    'option_b'       => 'Posting',
                    'option_c'       => 'Rekonsiliasi',
                    'option_d'       => 'Penyesuaian',
                    'correct_answer' => 'b',
                    'explanation'    => 'Posting adalah proses pemindahan (transfer) catatan dari jurnal umum ke akun-akun dalam buku besar.',
                    'quiz_order'     => 1,
                ],
                [
                    'question'       => 'Pembelian perlengkapan Rp 500.000 secara tunai. Jurnal yang tepat adalah...',
                    'option_a'       => 'Debit Kas, Kredit Perlengkapan',
                    'option_b'       => 'Debit Perlengkapan, Kredit Utang',
                    'option_c'       => 'Debit Perlengkapan, Kredit Kas',
                    'option_d'       => 'Debit Kas, Kredit Utang',
                    'correct_answer' => 'c',
                    'explanation'    => 'Pembelian tunai: aset perlengkapan bertambah (Debit) dan kas berkurang (Kredit).',
                    'quiz_order'     => 2,
                ],
                [
                    'question'       => 'Fungsi utama buku besar adalah...',
                    'option_a'       => 'Mencatat transaksi secara kronologis',
                    'option_b'       => 'Menampilkan saldo setiap akun secara terpisah',
                    'option_c'       => 'Menyusun laporan keuangan akhir tahun',
                    'option_d'       => 'Menghitung pajak penghasilan',
                    'correct_answer' => 'b',
                    'explanation'    => 'Buku besar (ledger) menampilkan saldo masing-masing akun sehingga memudahkan analisis posisi keuangan setiap akun.',
                    'quiz_order'     => 3,
                ],
            ],
            'Laporan Keuangan Dasar' => [
                [
                    'question'       => 'Laporan yang menyajikan posisi keuangan pada tanggal tertentu adalah...',
                    'option_a'       => 'Laporan Laba Rugi',
                    'option_b'       => 'Laporan Arus Kas',
                    'option_c'       => 'Neraca',
                    'option_d'       => 'Laporan Perubahan Ekuitas',
                    'correct_answer' => 'c',
                    'explanation'    => 'Neraca (Balance Sheet) menyajikan aset, liabilitas, dan ekuitas pada satu titik waktu tertentu.',
                    'quiz_order'     => 1,
                ],
                [
                    'question'       => 'Laporan arus kas dibagi menjadi berapa aktivitas utama?',
                    'option_a'       => '2',
                    'option_b'       => '3',
                    'option_c'       => '4',
                    'option_d'       => '5',
                    'correct_answer' => 'b',
                    'explanation'    => 'Laporan arus kas terdiri dari 3 aktivitas: operasi, investasi, dan pendanaan.',
                    'quiz_order'     => 2,
                ],
            ],
            'Analisis Rasio Keuangan' => [
                [
                    'question'       => 'Rumus Current Ratio adalah...',
                    'option_a'       => 'Aset Tetap / Liabilitas Lancar',
                    'option_b'       => 'Aset Lancar / Liabilitas Lancar',
                    'option_c'       => 'Laba Bersih / Total Aset',
                    'option_d'       => 'Total Utang / Total Ekuitas',
                    'correct_answer' => 'b',
                    'explanation'    => 'Current Ratio = Aset Lancar / Liabilitas Lancar. Rasio ini mengukur kemampuan membayar utang jangka pendek.',
                    'quiz_order'     => 1,
                ],
            ],
            'Pengantar Perpajakan' => [
                [
                    'question'       => 'Sistem perpajakan Indonesia menggunakan sistem...',
                    'option_a'       => 'Official assessment',
                    'option_b'       => 'Withholding assessment',
                    'option_c'       => 'Self assessment',
                    'option_d'       => 'Direct assessment',
                    'correct_answer' => 'c',
                    'explanation'    => 'Indonesia menganut self-assessment system, di mana wajib pajak menghitung, menyetor, dan melaporkan pajaknya sendiri.',
                    'quiz_order'     => 1,
                ],
                [
                    'question'       => 'PPN merupakan jenis pajak...',
                    'option_a'       => 'Langsung dan subjektif',
                    'option_b'       => 'Tidak langsung dan objektif',
                    'option_c'       => 'Langsung dan objektif',
                    'option_d'       => 'Tidak langsung dan subjektif',
                    'correct_answer' => 'b',
                    'explanation'    => 'PPN adalah pajak tidak langsung (beban dapat dialihkan ke konsumen) dan bersifat objektif (dikenakan atas objek/barang-jasa tertentu).',
                    'quiz_order'     => 2,
                ],
            ],
        ];

        foreach ($quizMap as $sectionTitle => $quizzes) {
            $section = Section::where('title', $sectionTitle)->first();
            if (! $section) continue;

            foreach ($quizzes as $data) {
                $section->quizzes()->create(array_merge($data, ['is_active' => true]));
            }
        }
    }
}
