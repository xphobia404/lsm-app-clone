<?php

use App\Models\LearningSchema;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

test('admin can update quiz settings including manual passing score and randomize toggles', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $section = Section::create([
        'title' => 'Section Dasar',
        'passing_score' => 70,
        'randomize_questions' => false,
        'randomize_options' => false,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.sections.quizzes.settings', $section), [
        'passing_score' => 85,
        'randomize_questions' => '1',
        'randomize_options' => '1',
    ]);

    $response->assertSessionHas('success');
    $section->refresh();

    expect($section->passing_score)->toBe(85);
    expect($section->randomize_questions)->toBeTrue();
    expect($section->randomize_options)->toBeTrue();
});

test('quiz passing threshold respects the configured section passing score', function () {
    $user = User::factory()->create(['role' => 'user']);
    $schema = LearningSchema::create(['title' => 'Skema Belajar', 'is_active' => true]);
    $section = Section::create([
        'title' => 'Section Ujian',
        'passing_score' => 50, // 50% needed to pass
        'is_active' => true,
    ]);
    $section->learningSchemas()->attach($schema->id, ['section_order' => 1]);

    $q1 = Quiz::create([
        'section_id' => $section->id,
        'question' => 'Soal 1?',
        'option_a' => 'Benar',
        'option_b' => 'Salah',
        'correct_answer' => 'a',
        'quiz_order' => 1,
        'is_active' => true,
    ]);
    $q2 = Quiz::create([
        'section_id' => $section->id,
        'question' => 'Soal 2?',
        'option_a' => 'A',
        'option_b' => 'B',
        'correct_answer' => 'a',
        'quiz_order' => 2,
        'is_active' => true,
    ]);

    // User gets 1 out of 2 correct (50%) -> Passes because passing_score is 50%
    $response = $this->actingAs($user)->post(route('user.quizzes.submit', $section), [
        'answers' => [
            $q1->id => 'a', // correct
            $q2->id => 'b', // wrong
        ],
    ]);

    $response->assertStatus(200);
    $response->assertViewHas('passed', true);

    $attempt = QuizAttempt::where('user_id', $user->id)->first();
    expect($attempt)->not->toBeNull();
    expect($attempt->correct_answers)->toBe(1);
    expect($attempt->total_questions)->toBe(2);
    expect($attempt->passing_score)->toBe(50);
    expect($attempt->is_passed)->toBeTrue();
    expect($attempt->isPassed())->toBeTrue();

    // Now if section passing_score is changed to 80%, 1/2 correct fails
    $section->update(['passing_score' => 80]);

    $response2 = $this->actingAs($user)->post(route('user.quizzes.submit', $section), [
        'answers' => [
            $q1->id => 'a',
            $q2->id => 'b',
        ],
    ]);

    $response2->assertStatus(200);
    $response2->assertViewHas('passed', false);

    $latestAttempt = QuizAttempt::where('user_id', $user->id)->latest('id')->first();
    expect($latestAttempt->is_passed)->toBeFalse();
    expect($latestAttempt->isPassed())->toBeFalse();
});

test('quiz options can be shuffled while preserving answer associations', function () {
    $quiz = new Quiz([
        'question' => 'Apa modal kerja?',
        'option_a' => 'Opsi A',
        'option_b' => 'Opsi B',
        'option_c' => 'Opsi C',
        'option_d' => 'Opsi D',
        'correct_answer' => 'b',
    ]);

    $normal = $quiz->getOptions(false);
    expect(array_keys($normal))->toBe(['a', 'b', 'c', 'd']);

    $shuffled = $quiz->getOptions(true);
    // Values and keys must remain matching
    foreach ($shuffled as $key => $val) {
        expect($val)->toBe($normal[$key]);
    }
    expect(count($shuffled))->toBe(4);
});

test('admin can download quiz import csv template', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $section = Section::create(['title' => 'Section Keuangan', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(route('admin.sections.quizzes.template', $section));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('question,option_a,option_b,option_c,option_d,correct_answer,explanation');
});

test('admin can import quizzes via csv', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $section = Section::create(['title' => 'Section Manajemen', 'is_active' => true]);

    $csvContent = implode("\n", [
        'question,option_a,option_b,option_c,option_d,correct_answer,explanation',
        '"Soal pertama dari CSV?","Pilihan A","Pilihan B","Pilihan C","Pilihan D","A","Penjelasan soal 1"',
        '"Soal kedua boolean?","Benar","Salah","","","B","Penjelasan soal 2"',
    ]);

    $file = UploadedFile::fake()->createWithContent('quizzes.csv', $csvContent);

    $response = $this->actingAs($admin)->post(route('admin.sections.quizzes.import', $section), [
        'csv_file' => $file,
    ]);

    $response->assertRedirect(route('admin.sections.quizzes.index', $section));
    $response->assertSessionHas('success', 'Berhasil mengimpor 2 soal quiz.');

    $this->assertDatabaseHas('quizzes', [
        'section_id' => $section->id,
        'question' => 'Soal pertama dari CSV?',
        'option_a' => 'Pilihan A',
        'option_b' => 'Pilihan B',
        'option_c' => 'Pilihan C',
        'option_d' => 'Pilihan D',
        'correct_answer' => 'a',
    ]);

    $this->assertDatabaseHas('quizzes', [
        'section_id' => $section->id,
        'question' => 'Soal kedua boolean?',
        'option_a' => 'Benar',
        'option_b' => 'Salah',
        'correct_answer' => 'b',
    ]);
});

test('admin can export section quiz results to csv', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user', 'name' => 'Budi Santoso', 'username' => 'budis']);
    $section = Section::create(['title' => 'Section Pajak', 'passing_score' => 70, 'is_active' => true]);

    QuizAttempt::create([
        'user_id' => $user->id,
        'section_id' => $section->id,
        'total_questions' => 5,
        'correct_answers' => 4,
        'passing_score' => 70,
        'is_passed' => true,
        'attempted_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.sections.quizzes.export-attempts', $section));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    $content = $response->streamedContent();
    expect($content)->toContain('ID Percobaan');
    expect($content)->toContain('Tanggal Pengerjaan');
    expect($content)->toContain('Nama User');
    expect($content)->toContain('Budi Santoso');
    expect($content)->toContain('budis');
    expect($content)->toContain('Section Pajak');
    expect($content)->toContain('80%');
    expect($content)->toContain('Lulus');
});

test('admin can access global quiz attempts list and export all attempts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user', 'name' => 'Dewi Lestari', 'username' => 'dewil']);
    $section = Section::create(['title' => 'Section Hukum', 'passing_score' => 75, 'is_active' => true]);

    QuizAttempt::create([
        'user_id' => $user->id,
        'section_id' => $section->id,
        'total_questions' => 4,
        'correct_answers' => 1,
        'passing_score' => 75,
        'is_passed' => false,
        'attempted_at' => now(),
    ]);

    // View index page
    $indexResponse = $this->actingAs($admin)->get(route('admin.quiz-attempts.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Dewi Lestari');
    $indexResponse->assertSee('Section Hukum');
    $indexResponse->assertSee('Belum Lulus');

    // Global export
    $exportResponse = $this->actingAs($admin)->get(route('admin.quiz-attempts.export'));
    $exportResponse->assertStatus(200);
    $exportResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    $exportContent = $exportResponse->streamedContent();
    expect($exportContent)->toContain('Dewi Lestari');
    expect($exportContent)->toContain('Section Hukum');
    expect($exportContent)->toContain('Belum Lulus');
    expect($exportContent)->toContain('25%');
});

test('quiz import handles invalid rows and header errors gracefully', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $section = Section::create(['title' => 'Section Akuntansi', 'is_active' => true]);

    // 1. Missing required header
    $invalidHeaderCsv = UploadedFile::fake()->createWithContent('invalid.csv', "title,opt1,opt2\nTest,A,B");
    $response = $this->actingAs($admin)->post(route('admin.sections.quizzes.import', $section), [
        'csv_file' => $invalidHeaderCsv,
    ]);
    $response->assertRedirect(route('admin.sections.quizzes.index', $section));
    $response->assertSessionHas('import_errors');

    // 2. Invalid correct_answer or empty option C when correct_answer is C
    $invalidRowsCsv = implode("\n", [
        'question,option_a,option_b,option_c,option_d,correct_answer,explanation',
        '"Soal valid","Opsi A","Opsi B","","","a",""',
        '"Soal kunci tidak valid","Opsi A","Opsi B","","","z",""',
        '"Soal opsi C kosong tapi kunci C","Opsi A","Opsi B","","","c",""',
    ]);

    $file = UploadedFile::fake()->createWithContent('invalid_rows.csv', $invalidRowsCsv);
    $response2 = $this->actingAs($admin)->post(route('admin.sections.quizzes.import', $section), [
        'csv_file' => $file,
    ]);

    $response2->assertRedirect(route('admin.sections.quizzes.index', $section));
    $response2->assertSessionHas('success', 'Berhasil mengimpor 1 soal quiz.');
    $response2->assertSessionHas('import_errors');

    $errors = session('import_errors');
    expect($errors)->toHaveCount(2);
    expect($errors[0])->toContain('Kunci jawaban benar harus antara A, B, C, atau D');
    expect($errors[1])->toContain("Kunci jawaban 'C' dipilih namun Opsi C kosong");
});

test('non-admin user cannot export or modify quiz settings', function () {
    $user = User::factory()->create(['role' => 'user']);
    $section = Section::create(['title' => 'Section Terlarang', 'is_active' => true]);

    $this->actingAs($user)->get(route('admin.sections.quizzes.export-attempts', $section))
        ->assertRedirect(route('user.dashboard'));

    $this->actingAs($user)->patch(route('admin.sections.quizzes.settings', $section), [
        'passing_score' => 90,
    ])->assertRedirect(route('user.dashboard'));

    $this->actingAs($user)->get(route('admin.quiz-attempts.index'))
        ->assertRedirect(route('user.dashboard'));

    $this->actingAs($user)->get(route('admin.quiz-attempts.export'))
        ->assertRedirect(route('user.dashboard'));
});
