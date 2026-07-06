<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserProgressController extends Controller
{
    /**
     * Dipanggil via AJAX setiap user berpindah slide.
     *
     * Body JSON:
     *   slide_index   : int   — indeks slide yang baru saja dilihat (0-based)
     *   total_slides  : int   — total slide di section ini
     *   content_ids   : int[] — id konten yang sudah dibaca
     *   completed     : bool  — true HANYA dikirim saat user klik "Selesai" (tidak ada quiz)
     */
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'slide_index'  => 'required|integer|min:0',
            'total_slides' => 'required|integer|min:1',
            'content_ids'  => 'sometimes|array',
            'content_ids.*'=> 'integer',
            'completed'    => 'sometimes|boolean',
        ]);

        $userId      = auth()->id();
        $slideIndex  = (int) $request->slide_index;
        $totalSlides = (int) $request->total_slides;
        $forceComplete = (bool) $request->input('completed', false);

        $hasQuiz = $section->quizzes()->active()->exists();

        // Hitung persentase berdasar slide yang terbaca
        $readCount  = $slideIndex + 1;
        $percentage = min(100, round(($readCount / $totalSlides) * 100, 2));

        // === ATURAN COMPLETED ===
        // Jika ada quiz  → completed HANYA dari QuizController saat passed (bukan di sini)
        // Jika tidak ada quiz → completed saat front-end kirim completed=true (klik Selesai)
        if ($hasQuiz) {
            // Batasi max 99% selama quiz belum selesai,
            // supaya tidak pernah auto-complete dari sini
            $percentage = min($percentage, 99);
            $isCompleted = false;
        } else {
            $isCompleted = $forceComplete;
            if ($isCompleted) {
                $percentage = 100;
            }
        }

        $progress = UserProgress::firstOrNew([
            'user_id'    => $userId,
            'section_id' => $section->id,
        ]);

        // Jangan turunkan persentase saat user mundur slide
        $existingPct  = (float) ($progress->progress_percentage ?? 0);
        $wasCompleted = $progress->exists && $progress->status === 'completed';

        // Jika sudah completed sebelumnya (misal dari quiz), jaga status itu
        if ($wasCompleted) {
            return response()->json([
                'ok'         => true,
                'percentage' => $existingPct,
                'status'     => 'completed',
            ]);
        }

        $newPct = max($existingPct, $percentage);

        $progress->fill([
            'status'              => $isCompleted ? 'completed' : 'in_progress',
            'progress_percentage' => $newPct,
            'started_at'          => $progress->started_at ?? Carbon::now(),
            'completed_at'        => $isCompleted ? Carbon::now() : $progress->completed_at,
        ]);

        $progress->save();

        return response()->json([
            'ok'         => true,
            'percentage' => $newPct,
            'status'     => $progress->status,
        ]);
    }
}
