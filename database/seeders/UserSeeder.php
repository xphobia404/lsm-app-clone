<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@lsm.test'],
            [
                'name'      => 'Administrator',
                'username'  => 'admin',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Sample users
        $users = [
            ['name' => 'Budi Santoso',   'username' => 'budi',   'email' => 'budi@lsm.test'],
            ['name' => 'Siti Rahayu',    'username' => 'siti',   'email' => 'siti@lsm.test'],
            ['name' => 'Andi Wijaya',    'username' => 'andi',   'email' => 'andi@lsm.test'],
            ['name' => 'Dewi Lestari',   'username' => 'dewi',   'email' => 'dewi@lsm.test'],
            ['name' => 'Rizky Pratama',  'username' => 'rizky',  'email' => 'rizky@lsm.test'],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'  => Hash::make('password'),
                    'role'      => 'user',
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('  Users seeded: 1 admin + 5 users.');
    }
}
