<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CourseTypeSeeder::class,   // 1. master data course type
            UserSeeder::class,         // 2. users + course type assignment
            SectionSeeder::class,      // 3. sections (butuh course_type_id)
            QuizSeeder::class,         // 4. quiz per section
            UserProgressSeeder::class, // 5. progress & quiz attempts
        ]);
    }
}
