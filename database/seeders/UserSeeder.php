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
        // ─── Admin ────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'      => 'Administrator',
                'email'     => 'admin@lsm.test',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // ─── Ambil course types ───────────────────────────────────────────
        $umum    = CourseType::where('slug', 'umum')->first();
        $relawan = CourseType::where('slug', 'relawan')->first();
        $staf    = CourseType::where('slug', 'staf')->first();
        $manajer = CourseType::where('slug', 'manajer')->first();

        // ─── Sample users + assignment ke course type via pivot ───────────
        $users = [
            ['name' => 'Budi Santoso',    'username' => 'budi',    'email' => 'budi@lsm.test',    'course_types' => [$umum, $relawan]],
            ['name' => 'Siti Rahayu',     'username' => 'siti',    'email' => 'siti@lsm.test',    'course_types' => [$umum]],
            ['name' => 'Ahmad Fauzi',     'username' => 'ahmad',   'email' => 'ahmad@lsm.test',   'course_types' => [$relawan, $staf]],
            ['name' => 'Dewi Lestari',    'username' => 'dewi',    'email' => 'dewi@lsm.test',    'course_types' => [$umum]],
            ['name' => 'Rizky Pratama',   'username' => 'rizky',   'email' => 'rizky@lsm.test',   'course_types' => [$staf, $manajer]],
            ['name' => 'Nur Hidayah',     'username' => 'nurhida', 'email' => 'nurhida@lsm.test', 'course_types' => [$umum, $relawan]],
            ['name' => 'Eko Prasetyo',    'username' => 'eko',     'email' => 'eko@lsm.test',     'course_types' => [$relawan]],
            ['name' => 'Maya Sari',       'username' => 'maya',    'email' => 'maya@lsm.test',    'course_types' => [$umum, $staf]],
            ['name' => 'Hendra Wijaya',   'username' => 'hendra',  'email' => 'hendra@lsm.test',  'course_types' => [$manajer]],
            ['name' => 'Fitri Handayani', 'username' => 'fitri',   'email' => 'fitri@lsm.test',   'course_types' => [$umum]],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'name'      => $data['name'],
                    'email'     => $data['email'],
                    'password'  => Hash::make('user123'),
                    'role'      => 'user',
                    'is_active' => true,
                ]
            );

            // Sync many-to-many via pivot course_type_user
            $courseTypeIds = collect($data['course_types'])
                ->filter()                  // buang null jika slug tidak ditemukan
                ->pluck('id')
                ->toArray();

            $user->courseTypes()->sync($courseTypeIds);
        }
    }
}
