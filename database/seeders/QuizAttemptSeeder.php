<?php

namespace Database\Seeders;

use App\Models\QuizAttempt;
use App\Models\Section;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Database\Seeder;

class QuizAttemptSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->where('is_active', true)->get();

        // Hanya buat attempt untuk section yang sudah completed / in_progress
        $progresses = UserProgress::whereIn('status', ['completed', 'in_progress'])
            ->whereIn('user_id', $users->pluck('id'))
            ->with('section.quizzes')
            ->get();

        foreach ($progresses as $progress) {
            $quizzes = $progress->section->quizzes->where('is_active', true);
            if ($quizzes->isEmpty()) {
                continue;
            }

            $totalQuestions = $quizzes->count();
            // Simulasi score: completed = 70-100%, in_progress = 30-70%
            $correctCount = $progress->status === 'completed'
                ? rand((int) ($totalQuestions * 0.7), $totalQuestions)
                : rand((int) ($totalQuestions * 0.3), (int) ($totalQuestions * 0.7));

            $passingScore = $progress->section->passing_score ?? 70;
            $scorePct = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;
            $passed = $scorePct >= $passingScore;

            // Simulasi detail jawaban user vs actual
            $simulatedAnswers = [];
            $quizList = $quizzes->values();
            $correctIndices = (array) array_rand(range(0, $totalQuestions - 1), max(1, min($correctCount, $totalQuestions)));

            foreach ($quizList as $idx => $q) {
                $actAns = strtolower($q->correct_answer ?: 'a');
                $isCorrect = in_array($idx, $correctIndices, true);
                if ($isCorrect) {
                    $uAns = $actAns;
                } else {
                    $wrongOpts = array_diff(['a', 'b', 'c', 'd'], [$actAns]);
                    $uAns = $wrongOpts[array_rand($wrongOpts)];
                }

                $simulatedAnswers[] = [
                    'quiz_id' => $q->id,
                    'quiz_number' => $idx + 1,
                    'question' => $q->question,
                    'user_answer' => strtoupper($uAns),
                    'user_answer_text' => $q->{'option_'.$uAns} ?? null,
                    'correct_answer' => strtoupper($actAns),
                    'correct_answer_text' => $q->{'option_'.$actAns} ?? null,
                    'is_correct' => $isCorrect,
                ];
            }

            QuizAttempt::create([
                'user_id' => $progress->user_id,
                'section_id' => $progress->section_id,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctCount,
                'passing_score' => $passingScore,
                'is_passed' => $passed,
                'answers' => $simulatedAnswers,
                'attempted_at' => $progress->started_at
                    ? $progress->started_at->addMinutes(rand(10, 60))
                    : now()->subDays(rand(1, 20)),
            ]);
        }
    }
}
