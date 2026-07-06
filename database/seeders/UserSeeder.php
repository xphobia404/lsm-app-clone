<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────
        User::create([
            'name'      => 'Super Admin',
            'username'  => 'admin',
            'email'     => 'admin@lsm.test',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // ── Users ─────────────────────────────────────────────────────
        $users = [
            ['name' => 'Budi Santoso',   'username' => 'budi',   'email' => 'budi@lsm.test'],
            ['name' => 'Siti Rahayu',    'username' => 'siti',   'email' => 'siti@lsm.test'],
            ['name' => 'Agus Pratama',   'username' => 'agus',   'email' => 'agus@lsm.test'],
            ['name' => 'Dewi Lestari',   'username' => 'dewi',   'email' => 'dewi@lsm.test'],
            ['name' => 'Rizky Maulana',  'username' => 'rizky',  'email' => 'rizky@lsm.test'],
            ['name' => 'Nanda Putri',    'username' => 'nanda',  'email' => 'nanda@lsm.test'],
            ['name' => 'Hendra Wijaya',  'username' => 'hendra', 'email' => 'hendra@lsm.test'],
            // 1 user non-aktif untuk testing
            ['name' => 'User Nonaktif',  'username' => 'nonaktif', 'email' => 'nonaktif@lsm.test', 'is_active' => false],
        ];

        foreach ($users as $data) {
            User::create(array_merge([
                'password'  => Hash::make('password'),
                'role'      => 'user',
                'is_active' => true,
            ], $data));
        }
    }
}
