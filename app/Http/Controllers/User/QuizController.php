<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Section;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // =========================================================================
    // Show Quiz
    // =========================================================================

    public function show(Section $section)
    {
        if (! $section->is_published) {
            abort(404);
        }

        $user = auth()->user();
        $this->authorizeAccess($user, $section);

        $progress = $this->getProgress($user->id, $section->id);

        // Sudah lulus → tidak perlu quiz lagi
        $lastAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->orderByDesc('attempt_number')
            ->first();

        if ($lastAttempt?->passed) {
            return redirect()->route('user.section.show', $section)
                ->with('info', 'Anda sudah lulus quiz ini.');
        }

        $quizzes = $section->quizzes()->orderBy('order')->get();

        if ($quizzes->isEmpty()) {
            return redirect()->route('user.section.show', $section)
                ->with('info', 'Section ini tidak memiliki quiz.');
        }

        $attemptNumber = QuizAttempt::nextAttemptNumber($user->id, $section->id);

        return view('user.quiz', compact('section', 'quizzes', 'lastAttempt', 'attemptNumber'));
    }

    // =========================================================================
    // Submit Quiz
    // =========================================================================

    public function submit(Request $request, Section $section)
    {
        if (! $section->is_published) {
            abort(404);
        }

        $user = auth()->user();
        $this->authorizeAccess($user, $section);
        $this->getProgress($user->id, $section->id);

        $quizzes = $section->quizzes()->orderBy('order')->get();

        // Validasi: semua soal wajib dijawab
        $request->validate(
            $quizzes->mapWithKeys(fn ($q) => ["answers.{$q->id}" => 'required|in:a,b,c,d'])->toArray(),
            $quizzes->mapWithKeys(fn ($q) => ["answers.{$q->id}.required" => 'Semua soal wajib dijawab.'])->toArray()
        );

        $answers        = $request->input('answers', []);
        $totalQuestions = $quizzes->count();
        $correctCount   = $quizzes->filter(
            fn ($q) => strtolower($answers[$q->id] ?? '') === strtolower($q->correct_answer)
        )->count();

        $passingScore = $section->passing_score ?? 100;
        $scorePercent = $totalQuestions > 0
            ? (int) round(($correctCount / $totalQuestions) * 100)
            : 0;
        $passed = $scorePercent >= $passingScore;

        // Simpan attempt
        $attemptNumber = QuizAttempt::nextAttemptNumber($user->id, $section->id);

        QuizAttempt::create([
            'user_id'        => $user->id,
            'section_id'     => $section->id,
            'attempt_number' => $attemptNumber,
            'answers'        => $answers,
            'score'          => $correctCount,
            'score_percent'  => $scorePercent,
            'passed'         => $passed,
            'submitted_at'   => now(),
        ]);

        if ($passed) {
            // Tandai section selesai & quiz lulus via model method
            $progress = UserProgress::where('user_id', $user->id)
                ->where('section_id', $section->id)
                ->first();

            $progress->markCompleted();
            $progress->markQuizPassed();

            // Unlock section berikutnya (dalam spesialisasi yang sama)
            $nextSection = Section::where('is_published', true)
                ->where('course_type_id', $section->course_type_id)
                ->where('order', '>', $section->order)
                ->orderBy('order')
                ->first();

            if ($nextSection) {
                UserProgress::updateOrCreate(
                    ['user_id' => $user->id, 'section_id' => $nextSection->id],
                    ['unlocked' => true, 'status' => 'not_started']
                );

                return redirect()->route('user.section.show', $nextSection)
                    ->with('success', '🎉 Selamat! Kamu lulus. Lanjut ke section berikutnya.');
            }

            return redirect()->route('user.courses')
                ->with('success', '🏆 Selamat! Kamu telah menyelesaikan semua materi di spesialisasi ini.');
        }

        return redirect()->route('user.quiz.show', $section)
            ->with('error', "Belum lulus. Skor kamu: {$scorePercent}% (min: {$passingScore}%). Coba lagi ya!");
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Gate: pastikan section ini termasuk spesialisasi user.
     */
    private function authorizeAccess($user, Section $section): void
    {
        $userCourseTypeIds = $user->courseTypes()->pluck('course_types.id');

        if ($userCourseTypeIds->isNotEmpty()
            && ! $userCourseTypeIds->contains($section->course_type_id)
        ) {
            abort(403, 'Kamu tidak memiliki akses ke quiz ini.');
        }
    }

    /**
     * Ambil progress user untuk section ini, abort 403 jika belum unlock.
     */
    private function getProgress(int $userId, int $sectionId): UserProgress
    {
        $progress = UserProgress::where('user_id', $userId)
            ->where('section_id', $sectionId)
            ->first();

        if (! $progress || ! $progress->unlocked) {
            abort(403, 'Section ini belum terbuka.');
        }

        return $progress;
    }
}
