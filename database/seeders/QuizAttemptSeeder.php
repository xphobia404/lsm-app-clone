<?php

namespace Database\Seeders;

use App\Models\Quiz;
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
            if ($quizzes->isEmpty()) continue;

            $totalQuestions = $quizzes->count();
            // Simulasi score: completed = 70-100%, in_progress = 30-70%
            $correctCount = $progress->status === 'completed'
                ? rand((int)($totalQuestions * 0.7), $totalQuestions)
                : rand((int)($totalQuestions * 0.3), (int)($totalQuestions * 0.7));

            QuizAttempt::create([
                'user_id'         => $progress->user_id,
                'section_id'      => $progress->section_id,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctCount,
                'attempted_at'    => $progress->started_at
                    ? $progress->started_at->addMinutes(rand(10, 60))
                    : now()->subDays(rand(1, 20)),
            ]);
        }
    }
}
