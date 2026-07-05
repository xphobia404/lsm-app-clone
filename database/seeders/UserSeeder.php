<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CourseType;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'      => 'Administrator',
                'username'  => 'admin',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Ambil course types
        $umum    = CourseType::where('slug', 'umum')->first();
        $relawan = CourseType::where('slug', 'relawan')->first();
        $staf    = CourseType::where('slug', 'staf')->first();
        $manajer = CourseType::where('slug', 'manajer')->first();

        // Sample users + course type assignment
        $users = [
            ['name' => 'Budi Santoso',    'username' => 'budi',    'course_types' => [$umum, $relawan]],
            ['name' => 'Siti Rahayu',     'username' => 'siti',    'course_types' => [$umum]],
            ['name' => 'Ahmad Fauzi',     'username' => 'ahmad',   'course_types' => [$relawan, $staf]],
            ['name' => 'Dewi Lestari',    'username' => 'dewi',    'course_types' => [$umum]],
            ['name' => 'Rizky Pratama',   'username' => 'rizky',   'course_types' => [$staf, $manajer]],
            ['name' => 'Nur Hidayah',     'username' => 'nurhida', 'course_types' => [$umum, $relawan]],
            ['name' => 'Eko Prasetyo',    'username' => 'eko',     'course_types' => [$relawan]],
            ['name' => 'Maya Sari',       'username' => 'maya',    'course_types' => [$umum, $staf]],
            ['name' => 'Hendra Wijaya',   'username' => 'hendra',  'course_types' => [$manajer]],
            ['name' => 'Fitri Handayani', 'username' => 'fitri',   'course_types' => [$umum]],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'name'      => $data['name'],
                    'username'  => $data['username'],
                    'password'  => Hash::make('user123'),
                    'role'      => 'user',
                    'is_active' => true,
                ]
            );

            // Sync many-to-many course types (filter null jika slug tidak ditemukan)
            $courseTypeIds = collect($data['course_types'])
                ->filter()
                ->pluck('id')
                ->toArray();

            $user->courseTypes()->sync($courseTypeIds);
        }
    }
}
