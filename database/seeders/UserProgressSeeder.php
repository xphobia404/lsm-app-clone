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
        $users    = User::where('role', 'user')->get();
        $sections = Section::orderBy('order')->get();

        if ($sections->isEmpty() || $users->isEmpty()) return;

        $firstSection = $sections->first();

        // Semua user unlock section pertama (not_started)
        foreach ($users as $user) {
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

        // Simulasi progress per user [username => sections_completed]
        $scenarios = [
            'budi'   => 3,
            'siti'   => 2,
            'ahmad'  => 4,
            'dewi'   => 1,
            'rizky'  => 5,
        ];

        foreach ($scenarios as $username => $completedUntil) {
            $user = User::where('username', $username)->first();
            if (!$user) continue;

            foreach ($sections as $section) {
                $isCompleted = $section->order <= $completedUntil;
                $isUnlocked  = $section->order <= ($completedUntil + 1);

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
                    $quizzes     = Quiz::where('section_id', $section->id)->get();
                    $totalSoal   = $quizzes->count();
                    $submittedAt = $completedAt ?? Carbon::now();

                    if ($totalSoal === 0) continue;

                    $quizIds = $quizzes->pluck('id');

                    // Attempt 1 - gagal (40% salah)
                    $wrongCount1   = max(1, (int) ($totalSoal * 0.4));
                    $correctCount1 = $totalSoal - $wrongCount1;
                    $scorePercent1 = (int) ($correctCount1 / $totalSoal * 100);
                    $answers1 = [];
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

                    // Attempt 2 - lulus (semua benar)
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
