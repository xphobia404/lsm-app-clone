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
     *   slide_index   : int  — indeks slide yang baru saja dilihat (0-based)
     *   total_slides  : int  — total slide di section ini
     *   content_ids   : int[] — id konten yang sudah dibaca (dikirim dari front-end)
     */
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'slide_index'  => 'required|integer|min:0',
            'total_slides' => 'required|integer|min:1',
            'content_ids'  => 'sometimes|array',
            'content_ids.*'=> 'integer',
        ]);

        $userId      = auth()->id();
        $slideIndex  = (int) $request->slide_index;   // slide yang baru saja dilihat
        $totalSlides = (int) $request->total_slides;

        // Hitung persentase: slide yg sudah dilihat / total slide
        // slideIndex dimulai dari 0, jadi slide ke-1 = index 0 → 1 konten terbaca
        $readCount   = $slideIndex + 1;  // sudah lihat sampai slide ini
        $percentage  = min(100, round(($readCount / $totalSlides) * 100, 2));

        $isCompleted = $percentage >= 100;

        $progress = UserProgress::firstOrNew([
            'user_id'    => $userId,
            'section_id' => $section->id,
        ]);

        // Jangan turunkan persentase kalau user mundur slide
        $existingPct = (float) ($progress->progress_percentage ?? 0);
        $newPct      = max($existingPct, $percentage);
        $wasCompleted = $progress->exists && $progress->status === 'completed';

        $progress->fill([
            'status'              => $isCompleted || $wasCompleted ? 'completed' : 'in_progress',
            'progress_percentage' => $newPct,
            'started_at'          => $progress->started_at ?? Carbon::now(),
            'completed_at'        => ($isCompleted && !$wasCompleted) ? Carbon::now() : $progress->completed_at,
        ]);

        $progress->save();

        return response()->json([
            'ok'          => true,
            'percentage'  => $newPct,
            'status'      => $progress->status,
        ]);
    }
}
