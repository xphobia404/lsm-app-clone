<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\CourseType;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $umum    = CourseType::where('slug', 'umum')->first();
        $relawan = CourseType::where('slug', 'relawan')->first();
        $staf    = CourseType::where('slug', 'staf')->first();
        $manajer = CourseType::where('slug', 'manajer')->first();

        $sections = [

            // ─── UMUM (semua anggota) ───────────────────────────────────────
            [
                'title'          => 'Pengenalan LSM dan Dunia Sosial',
                'description'    => 'Memahami apa itu LSM, sejarahnya, dan perannya di masyarakat.',
                'content'        => '<h2>Apa Itu LSM?</h2><p>Lembaga Swadaya Masyarakat (LSM) adalah organisasi nirlaba yang bergerak di bidang sosial, kemanusiaan, lingkungan, atau advokasi kebijakan. LSM beroperasi secara independen dari pemerintah dan sektor swasta.</p><h3>Sejarah Singkat</h3><p>LSM modern mulai berkembang pesat setelah Perang Dunia II. Di Indonesia, LSM tumbuh signifikan sejak era reformasi 1998 sebagai pilar masyarakat sipil.</p><h3>Tujuan Pembelajaran</h3><ul><li>Memahami definisi dan ciri khas LSM</li><li>Mengenal perbedaan LSM dengan yayasan dan ormas</li><li>Mengetahui peran LSM dalam ekosistem sosial Indonesia</li></ul>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 1,
                'is_published'   => true,
                'course_type_id' => $umum?->id,
            ],
            [
                'title'          => 'Nilai dan Etika Berorganisasi',
                'description'    => 'Prinsip-prinsip etika dan nilai yang menjadi fondasi kerja di LSM.',
                'content'        => '<h2>Nilai Dasar Organisasi</h2><p>Setiap anggota LSM wajib menjunjung tinggi nilai integritas, transparansi, akuntabilitas, dan keberpihakan pada masyarakat yang dilayani.</p><h3>Kode Etik Anggota</h3><ul><li>Jujur dan tidak korupsi</li><li>Menghormati privasi dan dignitas penerima manfaat</li><li>Tidak diskriminatif berdasarkan suku, agama, ras, atau jenis kelamin</li><li>Menjaga kerahasiaan informasi sensitif</li></ul><h3>Konflik Kepentingan</h3><p>Pahami bagaimana mengenali dan menghindari konflik kepentingan dalam pekerjaan sehari-hari.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 2,
                'is_published'   => true,
                'course_type_id' => $umum?->id,
            ],
            [
                'title'          => 'Struktur Organisasi dan Tata Kelola',
                'description'    => 'Memahami hierarki, divisi, dan alur kerja di dalam organisasi LSM.',
                'content'        => '<h2>Struktur Organisasi LSM</h2><p>Pemahaman terhadap struktur organisasi membantu setiap anggota mengetahui peran, tanggung jawab, dan kepada siapa mereka melapor.</p><h3>Komponen Utama</h3><ul><li><strong>Dewan Pengurus:</strong> Pemegang otoritas tertinggi dan pengarah kebijakan strategis</li><li><strong>Direktur Eksekutif:</strong> Pemimpin operasional harian</li><li><strong>Divisi Program:</strong> Perencanaan dan pelaksanaan kegiatan lapangan</li><li><strong>Divisi Keuangan & Administrasi:</strong> Pengelolaan sumber daya</li><li><strong>Divisi Komunikasi:</strong> Publikasi dan hubungan pemangku kepentingan</li></ul><h3>Alur Pelaporan</h3><p>Laporan program mengalir dari staf lapangan → koordinator program → direktur → dewan pengurus secara berkala.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 3,
                'is_published'   => true,
                'course_type_id' => $umum?->id,
            ],
            [
                'title'          => 'Komunikasi Efektif di Lingkungan Kerja',
                'description'    => 'Teknik komunikasi verbal dan tertulis untuk kolaborasi tim yang produktif.',
                'content'        => '<h2>Komunikasi Efektif</h2><p>Komunikasi yang baik adalah kunci keberhasilan setiap program. Ini mencakup cara menyampaikan informasi, mendengarkan aktif, dan menulis laporan yang jelas.</p><h3>Komunikasi Verbal</h3><ul><li>Teknik presentasi kepada tim dan pemangku kepentingan</li><li>Fasilitasi diskusi kelompok</li><li>Negosiasi dan mediasi sederhana</li></ul><h3>Komunikasi Tertulis</h3><ul><li>Penulisan email profesional</li><li>Menyusun notulensi rapat yang baik</li><li>Pembuatan laporan ringkas dan padat</li></ul>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 4,
                'is_published'   => true,
                'course_type_id' => $umum?->id,
            ],

            // ─── RELAWAN ───────────────────────────────────────────────────
            [
                'title'          => 'Orientasi Relawan: Peran dan Tanggung Jawab',
                'description'    => 'Panduan komprehensif untuk relawan baru tentang peran, hak, dan kewajiban di lapangan.',
                'content'        => '<h2>Selamat Bergabung, Relawan!</h2><p>Sebagai relawan, Anda adalah ujung tombak organisasi. Pemahaman terhadap peran Anda sangat penting untuk kelancaran program.</p><h3>Hak Relawan</h3><ul><li>Mendapat pelatihan dan pembekalan sebelum bertugas</li><li>Dilindungi secara hukum selama menjalankan tugas resmi LSM</li><li>Mendapat akses informasi program yang relevan</li></ul><h3>Kewajiban Relawan</h3><ul><li>Hadir dan tepat waktu sesuai jadwal yang disepakati</li><li>Menjaga citra dan nama baik organisasi</li><li>Melaporkan setiap kejadian penting kepada koordinator</li><li>Mengikuti prosedur keamanan dan keselamatan</li></ul>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 5,
                'is_published'   => true,
                'course_type_id' => $relawan?->id,
            ],
            [
                'title'          => 'Teknik Pendekatan dan Komunikasi dengan Komunitas',
                'description'    => 'Cara membangun kepercayaan dan berkomunikasi efektif dengan masyarakat penerima manfaat.',
                'content'        => '<h2>Pendekatan Komunitas</h2><p>Membangun kepercayaan komunitas adalah proses yang memerlukan kesabaran, empati, dan konsistensi. Relawan perlu memahami konteks sosial budaya setempat.</p><h3>Prinsip Pendekatan Partisipatif</h3><ul><li>Dengarkan sebelum berbicara — pahami kebutuhan nyata mereka</li><li>Hormati kearifan lokal dan budaya setempat</li><li>Libatkan tokoh masyarakat dalam setiap tahapan</li><li>Hindari posisi "donor yang superior"</li></ul><h3>Teknik Wawancara Lapangan</h3><p>Gunakan pertanyaan terbuka, hindari pertanyaan yang menggiring opini, dan catat respons dengan akurat tanpa interpretasi berlebihan.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 6,
                'is_published'   => true,
                'course_type_id' => $relawan?->id,
            ],
            [
                'title'          => 'Keselamatan dan Perlindungan di Lapangan',
                'description'    => 'Prosedur keselamatan, perlindungan diri, dan penanganan situasi darurat saat bertugas.',
                'content'        => '<h2>Keselamatan Relawan di Lapangan</h2><p>Keselamatan relawan adalah prioritas utama. Tidak ada program yang sebanding nilainya dengan keselamatan jiwa anggota tim.</p><h3>Penilaian Risiko Sebelum Tugas</h3><ul><li>Identifikasi potensi bahaya di lokasi penugasan</li><li>Pastikan ada kontak darurat yang dapat dihubungi</li><li>Bawa perlengkapan P3K dasar</li></ul><h3>Protokol Darurat</h3><ul><li>Nomor darurat yang harus diketahui (koordinator, rumah sakit, polisi)</li><li>Prosedur evakuasi jika situasi tidak aman</li><li>Cara melaporkan insiden kepada organisasi</li></ul><h3>Perlindungan Data Komunitas</h3><p>Jangan pernah membagikan identitas atau foto penerima manfaat tanpa izin tertulis yang jelas.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 7,
                'is_published'   => true,
                'course_type_id' => $relawan?->id,
            ],
            [
                'title'          => 'Dokumentasi dan Pelaporan Kegiatan Relawan',
                'description'    => 'Cara mendokumentasikan kegiatan lapangan dan menyusun laporan harian/mingguan.',
                'content'        => '<h2>Pentingnya Dokumentasi</h2><p>Dokumentasi yang baik adalah bukti kerja nyata dan menjadi dasar evaluasi program. Relawan bertanggung jawab untuk mencatat kegiatan secara akurat.</p><h3>Apa yang Harus Didokumentasikan</h3><ul><li>Jumlah penerima manfaat yang terlayani (beserta data demografis dasar)</li><li>Aktivitas yang dilakukan dan durasinya</li><li>Hambatan atau masalah yang ditemui</li><li>Foto kegiatan (dengan persetujuan yang bersangkutan)</li></ul><h3>Format Laporan Harian</h3><p>Gunakan template yang disediakan organisasi. Laporan harus diserahkan maksimal 24 jam setelah kegiatan berlangsung.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 8,
                'is_published'   => true,
                'course_type_id' => $relawan?->id,
            ],

            // ─── STAF ──────────────────────────────────────────────────────
            [
                'title'          => 'Manajemen Program: Perencanaan dan Pelaksanaan',
                'description'    => 'Metodologi perencanaan program berbasis kebutuhan, logframe, dan manajemen risiko.',
                'content'        => '<h2>Siklus Manajemen Program</h2><p>Setiap program LSM mengikuti siklus: identifikasi kebutuhan → perencanaan → pelaksanaan → monitoring → evaluasi. Staf program harus mahir di seluruh tahapan ini.</p><h3>Logical Framework (Logframe)</h3><p>Logframe adalah alat perencanaan yang menghubungkan input, aktivitas, output, outcome, dan dampak program secara logis dan terukur.</p><h3>Manajemen Risiko Program</h3><ul><li>Identifikasi risiko potensial sebelum program dimulai</li><li>Buat rencana mitigasi untuk setiap risiko kritis</li><li>Monitor risiko secara berkala dan perbarui rencana jika perlu</li></ul>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 9,
                'is_published'   => true,
                'course_type_id' => $staf?->id,
            ],
            [
                'title'          => 'Manajemen Keuangan dan Anggaran Program',
                'description'    => 'Pengelolaan anggaran program, pencatatan keuangan, dan penyusunan laporan pertanggungjawaban.',
                'content'        => '<h2>Manajemen Keuangan LSM</h2><p>Transparansi keuangan adalah fondasi kepercayaan donor dan publik terhadap LSM. Staf wajib memahami prinsip dasar pengelolaan keuangan organisasi nirlaba.</p><h3>Prinsip Keuangan LSM</h3><ul><li><strong>Transparansi:</strong> Semua transaksi harus terdokumentasi dan dapat diaudit</li><li><strong>Akuntabilitas:</strong> Dana harus digunakan sesuai peruntukannya</li><li><strong>Efisiensi:</strong> Maksimalkan dampak dengan sumber daya yang ada</li></ul><h3>Proses Pertanggungjawaban</h3><ul><li>Kumpulkan semua bukti pengeluaran (kwitansi, faktur)</li><li>Catat dalam buku kas harian</li><li>Rekonsiliasi dengan laporan bank setiap bulan</li><li>Siapkan laporan keuangan untuk setiap periode grant</li></ul>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 10,
                'is_published'   => true,
                'course_type_id' => $staf?->id,
            ],
            [
                'title'          => 'Monitoring, Evaluasi, dan Pembelajaran (MEL)',
                'description'    => 'Sistem monitoring berbasis indikator, evaluasi program, dan budaya pembelajaran organisasi.',
                'content'        => '<h2>Sistem MEL dalam LSM</h2><p>Monitoring, Evaluasi, dan Pembelajaran (MEL) adalah sistem yang memastikan program berjalan sesuai rencana dan menghasilkan pembelajaran untuk perbaikan berkelanjutan.</p><h3>Monitoring vs Evaluasi</h3><ul><li><strong>Monitoring:</strong> Pemantauan rutin selama program berlangsung (mingguan/bulanan)</li><li><strong>Evaluasi:</strong> Penilaian mendalam di akhir atau pertengahan program</li></ul><h3>Indikator Kinerja Kunci (KPI)</h3><p>Setiap program harus memiliki indikator yang SMART: Specific, Measurable, Achievable, Relevant, Time-bound.</p><h3>Budaya Pembelajaran</h3><p>Dokumentasikan pelajaran baik dari keberhasilan maupun kegagalan program. Knowledge management adalah aset jangka panjang organisasi.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 11,
                'is_published'   => true,
                'course_type_id' => $staf?->id,
            ],
            [
                'title'          => 'Penulisan Proposal dan Laporan Donor',
                'description'    => 'Teknik menyusun proposal program yang kompetitif dan laporan pertanggungjawaban kepada donor.',
                'content'        => '<h2>Penulisan Proposal yang Efektif</h2><p>Proposal adalah pintu masuk untuk mendapatkan pendanaan. Proposal yang baik harus jelas, terukur, relevan, dan meyakinkan donor bahwa LSM Anda mampu melaksanakannya.</p><h3>Komponen Proposal Standar</h3><ul><li>Executive Summary (ringkasan eksekutif)</li><li>Analisis masalah berbasis data</li><li>Tujuan dan indikator keberhasilan</li><li>Metodologi dan rencana kegiatan</li><li>Anggaran rinci dan justifikasinya</li><li>Profil organisasi dan kapasitas tim</li></ul><h3>Laporan Kepada Donor</h3><p>Laporan harus mencerminkan pencapaian dibandingkan target, penjelasan deviasi, penggunaan anggaran, dan cerita dampak dari penerima manfaat.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 12,
                'is_published'   => true,
                'course_type_id' => $staf?->id,
            ],

            // ─── MANAJER ───────────────────────────────────────────────────
            [
                'title'          => 'Kepemimpinan Transformasional di Sektor Sosial',
                'description'    => 'Model kepemimpinan yang memotivasi tim menuju tujuan organisasi jangka panjang.',
                'content'        => '<h2>Kepemimpinan di LSM</h2><p>Pemimpin LSM menghadapi tantangan unik: memimpin dengan sumber daya terbatas, tim yang heterogen, dan tekanan dari berbagai pemangku kepentingan. Kepemimpinan transformasional adalah pendekatan yang paling relevan.</p><h3>4 Pilar Kepemimpinan Transformasional</h3><ul><li><strong>Idealized Influence:</strong> Jadi teladan yang diikuti tim</li><li><strong>Inspirational Motivation:</strong> Berikan visi yang memberi semangat</li><li><strong>Intellectual Stimulation:</strong> Dorong tim berpikir kritis dan inovatif</li><li><strong>Individualized Consideration:</strong> Perhatikan kebutuhan dan perkembangan tiap anggota</li></ul><h3>Mengelola Tim Lintas Generasi</h3><p>Pahami karakteristik berbeda antar generasi (Millennial, Gen Z) dan ciptakan lingkungan inklusif yang menghargai keberagaman perspektif.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 13,
                'is_published'   => true,
                'course_type_id' => $manajer?->id,
            ],
            [
                'title'          => 'Perencanaan Strategis dan Pengembangan Organisasi',
                'description'    => 'Proses penyusunan rencana strategis, analisis SWOT, dan pengembangan kapasitas organisasi.',
                'content'        => '<h2>Perencanaan Strategis LSM</h2><p>Rencana strategis adalah peta jalan organisasi selama 3–5 tahun ke depan. Ini adalah dokumen hidup yang memandu keputusan operasional sehari-hari.</p><h3>Proses Penyusunan Renstra</h3><ul><li>Review mandat dan misi organisasi</li><li>Analisis lingkungan (SWOT/PESTLE)</li><li>Konsultasi pemangku kepentingan internal dan eksternal</li><li>Penetapan prioritas strategis dan tujuan jangka panjang</li><li>Penjabaran ke dalam rencana kerja tahunan</li></ul><h3>Pengembangan Kapasitas Organisasi</h3><p>Manajer bertanggung jawab membangun kapasitas tim melalui pelatihan, mentoring, dan sistem knowledge management yang kuat.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 14,
                'is_published'   => true,
                'course_type_id' => $manajer?->id,
            ],
            [
                'title'          => 'Manajemen Kemitraan dan Mobilisasi Sumber Daya',
                'description'    => 'Strategi membangun kemitraan strategis, penggalangan dana, dan diversifikasi sumber pendanaan.',
                'content'        => '<h2>Kemitraan Strategis</h2><p>Tidak ada LSM yang mampu bekerja sendirian. Kemitraan yang kuat dengan pemerintah, sektor swasta, akademisi, dan LSM lain adalah kunci keberlanjutan organisasi.</p><h3>Tipologi Kemitraan</h3><ul><li><strong>Kemitraan program:</strong> Kolaborasi dalam pelaksanaan kegiatan bersama</li><li><strong>Kemitraan pendanaan:</strong> CSR perusahaan, hibah bilateral/multilateral</li><li><strong>Kemitraan advokasi:</strong> Koalisi untuk mendorong perubahan kebijakan</li></ul><h3>Mobilisasi Sumber Daya</h3><p>Diversifikasi sumber pendanaan adalah kunci keberlanjutan. Kombinasikan pendanaan dari donor internasional, CSR lokal, fundraising publik, dan pendapatan mandiri organisasi.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 15,
                'is_published'   => true,
                'course_type_id' => $manajer?->id,
            ],
            [
                'title'          => 'Tata Kelola, Akuntabilitas, dan Keberlanjutan Organisasi',
                'description'    => 'Prinsip good governance, mekanisme akuntabilitas ke berbagai pihak, dan strategi keberlanjutan LSM.',
                'content'        => '<h2>Good Governance di LSM</h2><p>Tata kelola yang baik membedakan LSM yang kredibel dari yang tidak. Ini mencakup transparansi pengambilan keputusan, akuntabilitas keuangan, dan mekanisme pengawasan internal.</p><h3>Pilar Good Governance</h3><ul><li>Kejelasan peran dan tanggung jawab dewan vs manajemen</li><li>Kebijakan anti korupsi dan whistleblower protection</li><li>Audit keuangan eksternal tahunan</li><li>Keterbukaan informasi kepada publik dan donor</li></ul><h3>Akuntabilitas Downward</h3><p>Selain akuntabilitas ke donor (upward), LSM juga harus akuntabel ke penerima manfaat (downward accountability) — mekanisme umpan balik dan pengaduan yang mudah diakses komunitas.</p><h3>Strategi Keberlanjutan</h3><p>Rancang model keberlanjutan yang mengurangi ketergantungan pada satu donor: endowment fund, social enterprise, dan network pendanaan yang beragam.</p>',
                'media_type'     => 'video_upload',
                'media_file'     => null,
                'media_url'      => null,
                'thumbnail'      => null,
                'order'          => 16,
                'is_published'   => true,
                'course_type_id' => $manajer?->id,
            ],
        ];

        foreach ($sections as $data) {
            Section::updateOrCreate(
                ['order' => $data['order']],
                $data
            );
        }
    }
}
