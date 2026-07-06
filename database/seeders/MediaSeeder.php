<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Media;
use App\Models\Section;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // Thumbnail untuk beberapa section
        $sectionThumbs = [
            'Pengenalan Akuntansi'      => 'https://placehold.co/800x450/4F46E5/ffffff?text=Pengenalan+Akuntansi',
            'Persamaan Dasar Akuntansi' => 'https://placehold.co/800x450/0891B2/ffffff?text=Persamaan+Akuntansi',
            'Jurnal & Buku Besar'       => 'https://placehold.co/800x450/059669/ffffff?text=Jurnal+%26+Buku+Besar',
            'Laporan Keuangan Dasar'    => 'https://placehold.co/800x450/D97706/ffffff?text=Laporan+Keuangan',
        ];

        foreach ($sectionThumbs as $title => $url) {
            $section = Section::where('title', $title)->first();
            if (! $section) continue;

            $section->media()->create([
                'media_type' => 'url',
                'url'        => $url,
                'title'      => 'Thumbnail ' . $title,
                'is_active'  => true,
            ]);
        }

        // Media file dummy untuk beberapa konten
        Content::whereIn('title', [
            'Video: Pengenalan Akuntansi',
        ])->each(function ($content) {
            $content->media()->create([
                'media_type' => 'url',
                'url'        => 'https://www.youtube.com/watch?v=HMxVMaFvBFQ',
                'title'      => 'Video Pengenalan Akuntansi',
                'is_active'  => true,
            ]);
        });
    }
}
