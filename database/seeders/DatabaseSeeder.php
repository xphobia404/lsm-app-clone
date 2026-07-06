<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CourseTypeSeeder::class,   // 1. master: course types (umum, relawan, staf, manajer)
            UserSeeder::class,         // 2. users + assign ke course type via pivot
            SectionSeeder::class,      // 3. sections per course type (butuh admin user)
            QuizSeeder::class,         // 4. quiz per section
            UserProgressSeeder::class, // 5. simulasi progress & quiz attempts user
        ]);
    }
}
