<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Section;
use App\Models\UserProgress;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use Carbon\Carbon;

class UserProgressSeeder extends Seeder
{
    public function run(): void
    {
        $users    = User::where('role', 'user')->with('courseTypes')->get();
        $sections = Section::orderBy('order')->get();

        if ($sections->isEmpty() || $users->isEmpty()) return;

        foreach ($users as $user) {
            // Hanya section yang sesuai course_type user (via pivot)
            $userCourseTypeIds = $user->courseTypes->pluck('id');

            $userSections = $sections->filter(
                fn($s) => $s->course_type_id === null
                       || $userCourseTypeIds->contains($s->course_type_id)
            )->values();

            if ($userSections->isEmpty()) continue;

            // Unlock section pertama yang relevan
            $firstSection = $userSections->first();
            UserProgress::updateOrCreate(
                ['user_id' => $user->id, 'section_id' => $firstSection->id],
                [
                    'unlocked'       => true,
                    'status'         => 'not_started',
                    'completed_at'   => null,
                    'quiz_passed_at' => null,
                ]
            );
        }

        // ─── Simulasi progress per user ───────────────────────────────────
        // [username => berapa section yang sudah diselesaikan]
        $scenarios = [
            'budi'   => 3,
            'siti'   => 2,
            'ahmad'  => 4,
            'dewi'   => 1,
            'rizky'  => 5,
        ];

        foreach ($scenarios as $username => $completedUntil) {
            $user = User::where('username', $username)->with('courseTypes')->first();
            if (!$user) continue;

            $userCourseTypeIds = $user->courseTypes->pluck('id');
            $userSections = $sections->filter(
                fn($s) => $s->course_type_id === null
                       || $userCourseTypeIds->contains($s->course_type_id)
            )->values();

            foreach ($userSections as $index => $section) {
                $sectionOrder = $index + 1; // urutan relatif untuk user ini
                $isCompleted  = $sectionOrder <= $completedUntil;
                $isUnlocked   = $sectionOrder <= ($completedUntil + 1);

                if (!$isUnlocked) break;

                $completedAt  = $isCompleted ? Carbon::now()->subDays(rand(1, 10)) : null;
                $quizPassedAt = $isCompleted ? $completedAt : null;
                $status       = $isCompleted ? 'completed' : 'in_progress';

                UserProgress::updateOrCreate(
                    ['user_id' => $user->id, 'section_id' => $section->id],
                    [
                        'unlocked'       => $isUnlocked,
                        'status'         => $status,
                        'completed_at'   => $completedAt,
                        'quiz_passed_at' => $quizPassedAt,
                    ]
                );

                if ($isCompleted) {
                    $quizzes   = Quiz::where('section_id', $section->id)->get();
                    $totalSoal = $quizzes->count();

                    if ($totalSoal === 0) continue;

                    $submittedAt = $completedAt ?? Carbon::now();
                    $quizIds     = $quizzes->pluck('id');

                    // Attempt 1 — gagal (60% benar, tidak lulus)
                    $correctCount1 = (int) ($totalSoal * 0.6);
                    $scorePercent1 = (int) ($correctCount1 / $totalSoal * 100);
                    $answers1      = [];
                    foreach ($quizIds as $i => $qid) {
                        $answers1[(string) $qid] = $i < $correctCount1 ? 'a' : 'b';
                    }

                    QuizAttempt::updateOrCreate(
                        ['user_id' => $user->id, 'section_id' => $section->id, 'attempt_number' => 1],
                        [
                            'answers'       => json_encode($answers1),
                            'score'         => $correctCount1,
                            'score_percent' => $scorePercent1,
                            'passed'        => false,
                            'submitted_at'  => $submittedAt->copy()->subHours(2),
                        ]
                    );

                    // Attempt 2 — lulus (semua benar)
                    $answers2 = [];
                    foreach ($quizIds as $qid) {
                        $answers2[(string) $qid] = 'a';
                    }

                    QuizAttempt::updateOrCreate(
                        ['user_id' => $user->id, 'section_id' => $section->id, 'attempt_number' => 2],
                        [
                            'answers'       => json_encode($answers2),
                            'score'         => $totalSoal,
                            'score_percent' => 100,
                            'passed'        => true,
                            'submitted_at'  => $submittedAt,
                        ]
                    );
                }
            }
        }
    }
}
