<?php

namespace Database\Seeders;

use App\Models\LearningSchema;
use Illuminate\Database\Seeder;

class LearningSchemaSeeder extends Seeder
{
    public function run(): void
    {
        $schemas = $this->schemaData();

        foreach ($schemas as $schemaData) {
            $sections = $schemaData['sections'];
            unset($schemaData['sections']);

            $schema = LearningSchema::firstOrCreate(
                ['name' => $schemaData['name']],
                $schemaData
            );

            $this->seedSections($schema, $sections);
        }

        $this->command->info('  Learning schemas seeded: ' . count($schemas) . ' schemas.');
    }

    private function seedSections(LearningSchema $schema, array $sections): void
    {
        foreach ($sections as $order => $sectionData) {
            $contents = $sectionData['contents'] ?? [];
            $quizzes  = $sectionData['quizzes']  ?? [];
            unset($sectionData['contents'], $sectionData['quizzes']);

            $section = $schema->sections()->firstOrCreate(
                ['title' => $sectionData['title'], 'learning_schema_id' => $schema->id],
                array_merge($sectionData, ['section_order' => $order + 1])
            );

            $this->seedContents($section, $contents);
            $this->seedQuizzes($section, $quizzes);
        }
    }

    private function seedContents(\App\Models\Section $section, array $contents): void
    {
        foreach ($contents as $order => $contentData) {
            $section->contents()->firstOrCreate(
                ['section_id' => $section->id, 'content_order' => $order + 1],
                array_merge($contentData, ['content_order' => $order + 1])
            );
        }
    }

    private function seedQuizzes(\App\Models\Section $section, array $quizzes): void
    {
        foreach ($quizzes as $order => $quizData) {
            $section->quizzes()->firstOrCreate(
                ['section_id' => $section->id, 'quiz_order' => $order + 1],
                array_merge($quizData, ['quiz_order' => $order + 1])
            );
        }
    }

    // ---------------------------------------------------------------
    // DATA
    // ---------------------------------------------------------------
    private function schemaData(): array
    {
        return [
            // ── Schema 1: Laravel Fundamentals ──────────────────────
            [
                'name'        => 'Laravel Fundamentals',
                'description' => 'Belajar dasar-dasar framework Laravel dari awal hingga membangun REST API.',
                'is_active'   => true,
                'sections'    => [
                    [
                        'title'       => 'Pengenalan Laravel',
                        'description' => 'Sejarah, filosofi, dan instalasi Laravel.',
                        'is_active'   => true,
                        'contents'    => [
                            ['description' => "Laravel adalah framework PHP yang elegan dan ekspresif. Diciptakan oleh Taylor Otwell pada 2011, Laravel dirancang untuk membuat pengembangan web menjadi lebih menyenangkan tanpa mengorbankan fungsionalitas aplikasi.", 'is_active' => true],
                            ['description' => "## Instalasi\n\nPastikan PHP >= 8.2 dan Composer sudah terinstal.\n\n```bash\ncomposer create-project laravel/laravel nama-project\ncd nama-project\nphp artisan serve\n```", 'is_active' => true],
                        ],
                        'quizzes' => [
                            [
                                'question'       => 'Siapakah pencipta framework Laravel?',
                                'option_a'       => 'Rasmus Lerdorf',
                                'option_b'       => 'Taylor Otwell',
                                'option_c'       => 'Fabien Potencier',
                                'option_d'       => 'Evan You',
                                'correct_answer' => 'b',
                                'explanation'    => 'Taylor Otwell menciptakan Laravel pada tahun 2011.',
                                'is_active'      => true,
                            ],
                            [
                                'question'       => 'Versi PHP minimum yang dibutuhkan Laravel 11 adalah?',
                                'option_a'       => 'PHP 7.4',
                                'option_b'       => 'PHP 8.0',
                                'option_c'       => 'PHP 8.2',
                                'option_d'       => 'PHP 8.3',
                                'correct_answer' => 'c',
                                'explanation'    => 'Laravel 11 membutuhkan minimal PHP 8.2.',
                                'is_active'      => true,
                            ],
                        ],
                    ],
                    [
                        'title'       => 'Routing & Controller',
                        'description' => 'Memahami sistem routing Laravel dan cara membuat controller.',
                        'is_active'   => true,
                        'contents'    => [
                            ['description' => "## Routing Dasar\n\nSemua route didefinisikan di file `routes/web.php` atau `routes/api.php`.\n\n```php\nRoute::get('/hello', function () {\n    return 'Hello World!';\n});\n\nRoute::get('/users/{id}', [UserController::class, 'show']);\n```", 'is_active' => true],
                            ['description' => "## Membuat Controller\n\n```bash\nphp artisan make:controller UserController --resource\n```\n\nController resource otomatis membuat method: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.", 'is_active' => true],
                        ],
                        'quizzes' => [
                            [
                                'question'       => 'Perintah artisan untuk membuat resource controller adalah?',
                                'option_a'       => 'php artisan make:controller NamaController',
                                'option_b'       => 'php artisan make:controller NamaController --resource',
                                'option_c'       => 'php artisan create:controller NamaController',
                                'option_d'       => 'php artisan controller:make NamaController',
                                'correct_answer' => 'b',
                                'explanation'    => 'Flag --resource membuat semua method CRUD secara otomatis.',
                                'is_active'      => true,
                            ],
                        ],
                    ],
                    [
                        'title'       => 'Eloquent ORM',
                        'description' => 'Bekerja dengan database menggunakan Eloquent ORM.',
                        'is_active'   => true,
                        'contents'    => [
                            ['description' => "## Eloquent Model\n\nEloquent adalah Active Record ORM bawaan Laravel.\n\n```php\n// Mengambil semua data\n\$users = User::all();\n\n// Query dengan kondisi\n\$activeUsers = User::where('is_active', true)->get();\n\n// Membuat data baru\n\$user = User::create(['name' => 'John', 'email' => 'john@example.com']);\n```", 'is_active' => true],
                        ],
                        'quizzes' => [
                            [
                                'question'       => 'Method Eloquent yang digunakan untuk mengambil semua record adalah?',
                                'option_a'       => 'User::get()',
                                'option_b'       => 'User::fetch()',
                                'option_c'       => 'User::all()',
                                'option_d'       => 'User::find()',
                                'correct_answer' => 'c',
                                'explanation'    => 'User::all() mengembalikan semua record dari tabel users.',
                                'is_active'      => true,
                            ],
                        ],
                    ],
                ],
            ],

            // ── Schema 2: Vue.js 3 Essentials ───────────────────────
            [
                'name'        => 'Vue.js 3 Essentials',
                'description' => 'Belajar Vue.js 3 dengan Composition API dan TypeScript dari dasar.',
                'is_active'   => true,
                'sections'    => [
                    [
                        'title'       => 'Pengenalan Vue.js 3',
                        'description' => 'Konsep dasar Vue.js 3 dan Composition API.',
                        'is_active'   => true,
                        'contents'    => [
                            ['description' => "Vue.js 3 adalah progressive JavaScript framework untuk membangun user interface. Fitur utama Vue 3 adalah **Composition API** yang memberikan fleksibilitas lebih dalam mengorganisir logika komponen.", 'is_active' => true],
                            ['description' => "## Setup Project\n\n```bash\nnpm create vue@latest\ncd nama-project\nnpm install\nnpm run dev\n```", 'is_active' => true],
                        ],
                        'quizzes' => [
                            [
                                'question'       => 'API baru yang diperkenalkan di Vue 3 adalah?',
                                'option_a'       => 'Options API',
                                'option_b'       => 'Composition API',
                                'option_c'       => 'Component API',
                                'option_d'       => 'Reactive API',
                                'correct_answer' => 'b',
                                'explanation'    => 'Composition API adalah fitur baru di Vue 3 yang memungkinkan logika lebih terorganisir.',
                                'is_active'      => true,
                            ],
                        ],
                    ],
                    [
                        'title'       => 'Reactive & Ref',
                        'description' => 'Memahami sistem reaktivitas Vue 3 dengan ref() dan reactive().',
                        'is_active'   => true,
                        'contents'    => [
                            ['description' => "## ref() vs reactive()\n\n```typescript\nimport { ref, reactive } from 'vue'\n\n// ref untuk nilai primitif\nconst count = ref(0)\nconsole.log(count.value) // 0\n\n// reactive untuk objek\nconst state = reactive({\n  name: 'John',\n  age: 25\n})\nconsole.log(state.name) // John\n```", 'is_active' => true],
                        ],
                        'quizzes' => [
                            [
                                'question'       => 'Untuk mengakses nilai ref di dalam script setup, kita menggunakan?',
                                'option_a'       => 'count',
                                'option_b'       => 'count.value',
                                'option_c'       => 'count.get()',
                                'option_d'       => '$count',
                                'correct_answer' => 'b',
                                'explanation'    => 'Nilai ref diakses via .value di dalam script, namun otomatis di-unwrap di template.',
                                'is_active'      => true,
                            ],
                        ],
                    ],
                ],
            ],

            // ── Schema 3: RESTful API Design ─────────────────────────
            [
                'name'        => 'RESTful API Design',
                'description' => 'Prinsip dan praktik terbaik dalam merancang RESTful API yang baik.',
                'is_active'   => true,
                'sections'    => [
                    [
                        'title'       => 'Prinsip REST',
                        'description' => 'Memahami 6 constraint REST dan konvensi HTTP.',
                        'is_active'   => true,
                        'contents'    => [
                            ['description' => "REST (Representational State Transfer) adalah arsitektur API yang menggunakan HTTP secara konsisten. Setiap resource direpresentasikan oleh URL, dan operasi dilakukan menggunakan HTTP method: GET, POST, PUT/PATCH, DELETE.", 'is_active' => true],
                        ],
                        'quizzes' => [
                            [
                                'question'       => 'HTTP method yang tepat untuk mengupdate sebagian data resource adalah?',
                                'option_a'       => 'POST',
                                'option_b'       => 'PUT',
                                'option_c'       => 'PATCH',
                                'option_d'       => 'UPDATE',
                                'correct_answer' => 'c',
                                'explanation'    => 'PATCH digunakan untuk partial update, sedangkan PUT untuk full replacement.',
                                'is_active'      => true,
                            ],
                            [
                                'question'       => 'HTTP status code untuk resource yang berhasil dibuat adalah?',
                                'option_a'       => '200 OK',
                                'option_b'       => '201 Created',
                                'option_c'       => '204 No Content',
                                'option_d'       => '202 Accepted',
                                'correct_answer' => 'b',
                                'explanation'    => '201 Created digunakan saat resource baru berhasil dibuat (POST).',
                                'is_active'      => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
