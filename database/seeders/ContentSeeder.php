<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Section;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $contentMap = [
            'Pengenalan Akuntansi' => [
                ['title' => 'Apa itu Akuntansi?',           'type' => 'text',  'body' => 'Akuntansi adalah proses pencatatan, penggolongan, pengikhtisaran, dan pelaporan transaksi keuangan suatu entitas. Akuntansi sering disebut sebagai "bahasa bisnis" karena melalui laporan keuangan, berbagai pihak dapat memahami kondisi keuangan sebuah organisasi.', 'order' => 1],
                ['title' => 'Sejarah Singkat Akuntansi',    'type' => 'text',  'body' => 'Sistem pembukuan berpasangan (double-entry bookkeeping) pertama kali didokumentasikan oleh Luca Pacioli pada tahun 1494 dalam bukunya Summa de Arithmetica. Sistem ini menjadi landasan akuntansi modern yang digunakan hingga saat ini.', 'order' => 2],
                ['title' => 'Video: Pengenalan Akuntansi',  'type' => 'video', 'body' => null, 'url' => 'https://www.youtube.com/watch?v=dummyakuntansi', 'order' => 3],
            ],
            'Persamaan Dasar Akuntansi' => [
                ['title' => 'Konsep Aset = Liabilitas + Ekuitas', 'type' => 'text', 'body' => 'Persamaan dasar akuntansi: Aset = Liabilitas + Ekuitas. Setiap transaksi keuangan selalu menjaga keseimbangan persamaan ini. Aset adalah sumber daya yang dimiliki perusahaan, liabilitas adalah kewajiban kepada pihak lain, dan ekuitas adalah hak pemilik atas aset bersih.', 'order' => 1],
                ['title' => 'Contoh Transaksi & Dampaknya',      'type' => 'text', 'body' => 'Contoh: Jika pemilik menyetorkan modal Rp 10.000.000, maka Kas (Aset) bertambah Rp 10.000.000 dan Modal (Ekuitas) bertambah Rp 10.000.000. Keseimbangan tetap terjaga.', 'order' => 2],
            ],
            'Jurnal & Buku Besar' => [
                ['title' => 'Pengertian Jurnal Umum',   'type' => 'text', 'body' => 'Jurnal umum adalah catatan kronologis seluruh transaksi keuangan perusahaan. Setiap transaksi dicatat dengan format: tanggal, akun debit, akun kredit, dan keterangan.', 'order' => 1],
                ['title' => 'Contoh Pencatatan Jurnal', 'type' => 'text', 'body' => 'Pembelian perlengkapan senilai Rp 500.000 tunai: Debit Perlengkapan Rp 500.000 | Kredit Kas Rp 500.000. Catatan selalu seimbang antara sisi debit dan kredit.', 'order' => 2],
                ['title' => 'Posting ke Buku Besar',   'type' => 'text', 'body' => 'Setelah dijurnal, transaksi dipindahkan (posting) ke akun masing-masing di buku besar. Buku besar menampilkan saldo setiap akun secara terpisah sehingga mudah dianalisis.', 'order' => 3],
            ],
            'Laporan Keuangan Dasar' => [
                ['title' => 'Neraca (Balance Sheet)',         'type' => 'text', 'body' => 'Neraca menyajikan posisi keuangan perusahaan pada tanggal tertentu, terdiri dari: (1) Aset lancar & tidak lancar, (2) Liabilitas jangka pendek & panjang, (3) Ekuitas pemilik.', 'order' => 1],
                ['title' => 'Laporan Laba Rugi',              'type' => 'text', 'body' => 'Laporan laba rugi menunjukkan kinerja keuangan selama periode tertentu. Rumus sederhana: Laba Bersih = Pendapatan - Beban. Jika hasilnya negatif, perusahaan mengalami kerugian.', 'order' => 2],
                ['title' => 'Laporan Arus Kas',               'type' => 'text', 'body' => 'Laporan arus kas terbagi menjadi tiga aktivitas: (1) Aktivitas operasi, (2) Aktivitas investasi, (3) Aktivitas pendanaan. Laporan ini menunjukkan kemampuan perusahaan menghasilkan kas.', 'order' => 3],
            ],
            'Analisis Rasio Keuangan' => [
                ['title' => 'Rasio Likuiditas', 'type' => 'text', 'body' => 'Rasio likuiditas mengukur kemampuan perusahaan memenuhi kewajiban jangka pendek. Current Ratio = Aset Lancar / Liabilitas Lancar. Nilai ideal umumnya di atas 2,0.', 'order' => 1],
                ['title' => 'Rasio Profitabilitas', 'type' => 'text', 'body' => 'Return on Equity (ROE) = Laba Bersih / Total Ekuitas × 100%. Rasio ini mengukur seberapa efisien perusahaan menggunakan modal pemilik untuk menghasilkan laba.', 'order' => 2],
            ],
            'Pengantar Perpajakan' => [
                ['title' => 'Sistem Perpajakan di Indonesia', 'type' => 'text', 'body' => 'Indonesia menganut sistem self-assessment, di mana wajib pajak menghitung, menyetor, dan melaporkan pajak sendiri. Dasar hukum utama adalah UU KUP (Ketentuan Umum dan Tata Cara Perpajakan).', 'order' => 1],
                ['title' => 'Jenis-Jenis Pajak',             'type' => 'text', 'body' => 'Pajak dibedakan berdasarkan pihak yang menanggung (langsung/tidak langsung), lembaga pemungut (pusat/daerah), dan sifatnya (subjektif/objektif). Contoh: PPh (langsung, pusat, subjektif) dan PPN (tidak langsung, pusat, objektif).', 'order' => 2],
            ],
        ];

        foreach ($contentMap as $sectionTitle => $contents) {
            $section = Section::where('title', $sectionTitle)->first();
            if (! $section) continue;

            foreach ($contents as $data) {
                $section->contents()->create([
                    'title'         => $data['title'],
                    'content_type'  => $data['type'],
                    'body'          => $data['body'] ?? null,
                    'url'           => $data['url'] ?? null,
                    'content_order' => $data['order'],
                    'is_active'     => true,
                ]);
            }
        }
    }
}
