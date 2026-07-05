<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\UserProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Section $section)
    {
        if (!$section->is_published) abort(404);

        $user = auth()->user();

        // ── Gate: pastikan section ini termasuk spesialisasi user ──────────────
        $userCourseTypeIds = $user->courseTypes()->pluck('course_types.id');
        if ($userCourseTypeIds->isNotEmpty() && !$userCourseTypeIds->contains($section->course_type_id)) {
            return redirect()->route('user.courses')
                ->with('error', 'Kamu tidak memiliki akses ke quiz ini.');
        }

        $progress = UserProgress::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->first();

        if (!$progress || !$progress->unlocked) {
            return redirect()->route('user.courses')
                ->with('error', 'Section ini belum terbuka.');
        }

        // Cek apakah sudah pernah lulus
        $lastAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->orderByDesc('attempt_number')
            ->first();

        if ($lastAttempt && $lastAttempt->passed) {
            return redirect()->route('user.section.show', $section)
                ->with('info', 'Anda sudah lulus quiz ini.');
        }

        $quizzes = $section->quizzes()->orderBy('order')->get();

        if ($quizzes->isEmpty()) {
            return redirect()->route('user.section.show', $section)
                ->with('info', 'Section ini tidak memiliki quiz.');
        }

        $attemptNumber = $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;

        return view('user.quiz', compact('section', 'quizzes', 'lastAttempt', 'attemptNumber'));
    }

    public function submit(Request $request, Section $section)
    {
        if (!$section->is_published) abort(404);

        $user = auth()->user();

        // ── Gate: pastikan section ini termasuk spesialisasi user ──────────────
        $userCourseTypeIds = $user->courseTypes()->pluck('course_types.id');
        if ($userCourseTypeIds->isNotEmpty() && !$userCourseTypeIds->contains($section->course_type_id)) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $progress = UserProgress::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->first();

        if (!$progress || !$progress->unlocked) {
            abort(403);
        }

        $quizzes = $section->quizzes()->orderBy('order')->get();

        $request->validate(
            collect($quizzes)->mapWithKeys(fn($q) => ["answers.{$q->id}" => 'required|in:a,b,c,d'])->toArray(),
            collect($quizzes)->mapWithKeys(fn($q) => ["answers.{$q->id}.required" => 'Semua soal wajib dijawab.'])->toArray()
        );

        $answers        = $request->input('answers', []);
        $correctCount   = 0;
        $totalQuestions = $quizzes->count();

        foreach ($quizzes as $quiz) {
            if (strtolower($answers[$quiz->id] ?? '') === strtolower($quiz->correct_answer)) {
                $correctCount++;
            }
        }

        $passed       = ($correctCount === $totalQuestions);
        $scorePercent = $totalQuestions > 0
            ? (int) round(($correctCount / $totalQuestions) * 100)
            : 0;

        $lastAttempt   = QuizAttempt::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->orderByDesc('attempt_number')
            ->first();
        $attemptNumber = $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;

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
            // Tandai section ini selesai
            $progress->update([
                'status'         => 'completed',
                'completed_at'   => now(),
                'quiz_passed_at' => now(),
            ]);

            // Cari section berikutnya — HANYA dalam spesialisasi yang sama
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

            // Semua section di spesialisasi ini selesai
            return redirect()->route('user.courses')
                ->with('success', '🏆 Selamat! Kamu telah menyelesaikan semua materi di spesialisasi ini.');
        }

        // Gagal — balik ke quiz
        return redirect()->route('user.quiz.show', $section)
            ->with('error', "Belum semua benar. Skor kamu: {$scorePercent}%. Coba lagi ya!");
    }
}
