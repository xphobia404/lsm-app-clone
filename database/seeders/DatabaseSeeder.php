<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting — jangan dibalik karena ada foreign key dependency.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,            // 1. Users (admin + 8 user)
            LearningSchemaSeeder::class,  // 2. Learning Schemas (4 materi)
            SectionSeeder::class,         // 3. Sections + pivot ke schemas
            ContentSeeder::class,         // 4. Konten per section
            QuizSeeder::class,            // 5. Quiz per section
            MediaSeeder::class,           // 6. Media (thumbnail & video)
            UserProgressSeeder::class,    // 7. Progress belajar user
            QuizAttemptSeeder::class,     // 8. Hasil attempt quiz
        ]);
    }
}
