<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseType;

class CourseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name'        => 'Umum',
                'slug'        => 'umum',
                'description' => 'Materi pembelajaran umum untuk semua anggota LSM.',
                'icon'        => 'book-open',
                'is_active'   => true,
                'order'       => 1,
            ],
            [
                'name'        => 'Relawan',
                'slug'        => 'relawan',
                'description' => 'Kurikulum khusus untuk relawan lapangan LSM.',
                'icon'        => 'users',
                'is_active'   => true,
                'order'       => 2,
            ],
            [
                'name'        => 'Staf',
                'slug'        => 'staf',
                'description' => 'Materi lanjutan untuk staf dan pengurus LSM.',
                'icon'        => 'briefcase',
                'is_active'   => true,
                'order'       => 3,
            ],
            [
                'name'        => 'Manajer',
                'slug'        => 'manajer',
                'description' => 'Kurikulum kepemimpinan dan manajemen untuk level manajerial.',
                'icon'        => 'award',
                'is_active'   => true,
                'order'       => 4,
            ],
        ];

        foreach ($types as $data) {
            CourseType::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
