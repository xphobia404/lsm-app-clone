<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\UserProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function show(Section $section)
    {
        if (!$section->is_published) abort(404);

        $user = auth()->user();

        // ── Gate: pastikan section ini termasuk spesialisasi user ──────────────
        $userCourseTypeIds = $user->courseTypes()->pluck('course_types.id');

        // Jika user punya spesialisasi tapi section ini bukan bagiannya → tolak
        if ($userCourseTypeIds->isNotEmpty() && !$userCourseTypeIds->contains($section->course_type_id)) {
            return redirect()->route('user.courses')
                ->with('error', 'Kamu tidak memiliki akses ke materi ini.');
        }

        // ── Cek progress / unlock ───────────────────────────────────────────────
        $progress = UserProgress::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->first();

        if (!$progress || !$progress->unlocked) {
            return redirect()->route('user.courses')
                ->with('error', 'Section ini belum terbuka.');
        }

        // Set status to in_progress jika masih not_started
        if ($progress->status === 'not_started') {
            $progress->update(['status' => 'in_progress']);
        }

        // ── Navigasi prev/next — hanya dalam spesialisasi yang sama ────────────
        $siblingQuery = Section::where('is_published', true)
            ->where('course_type_id', $section->course_type_id)
            ->orderBy('order');

        $prevSection = (clone $siblingQuery)->where('order', '<', $section->order)->get()->last();
        $nextSection = (clone $siblingQuery)->where('order', '>', $section->order)->first();

        // Cek apakah quiz section ini sudah lulus
        $quizPassed = QuizAttempt::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->where('passed', true)
            ->exists();

        // Cek apakah next section sudah unlocked
        $nextUnlocked = false;
        if ($nextSection) {
            $nextUnlocked = UserProgress::where('user_id', $user->id)
                ->where('section_id', $nextSection->id)
                ->where('unlocked', true)
                ->exists();
        }

        $hasQuiz = $section->quizzes()->count() > 0;

        return view('user.section', compact(
            'section',
            'progress',
            'prevSection',
            'nextSection',
            'quizPassed',
            'nextUnlocked',
            'hasQuiz'
        ));
    }
}
