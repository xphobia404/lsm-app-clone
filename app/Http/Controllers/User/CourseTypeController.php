<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class CourseTypeController extends Controller
{
    /**
     * Tampilkan halaman pemilihan spesialisasi.
     */
    public function select()
    {
        $user        = auth()->user();
        $courseTypes = CourseType::active()
            ->withCount('sections')
            ->orderBy('order')
            ->get();

        // ID spesialisasi yang sudah dipilih user (untuk highlight di view)
        $selectedIds = $user->courseTypes()->pluck('course_types.id')->toArray();

        return view('user.course-type.select', compact('courseTypes', 'selectedIds'));
    }

    /**
     * Simpan pilihan spesialisasi user (via pivot, bukan course_type_id).
     * Jika user ganti spesialisasi yang sudah ada, reset progress lama.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_type_id' => 'required|exists:course_types,id',
        ]);

        $user      = auth()->user();
        $newTypeId = (int) $request->course_type_id;

        // Ambil spesialisasi lama user
        $oldTypeIds = $user->courseTypes()->pluck('course_types.id')->toArray();

        // Jika user sudah punya spesialisasi LAIN dan sekarang ganti ke yang baru:
        // reset progress + attempts dari spesialisasi lama yang tidak dipilih lagi
        if (! empty($oldTypeIds) && ! in_array($newTypeId, $oldTypeIds)) {
            // Hapus progress section-section dari spesialisasi lama
            $oldSectionIds = \App\Models\Section::whereIn('course_type_id', $oldTypeIds)
                ->pluck('id');

            UserProgress::where('user_id', $user->id)
                ->whereIn('section_id', $oldSectionIds)
                ->delete();

            \App\Models\QuizAttempt::where('user_id', $user->id)
                ->whereIn('section_id', $oldSectionIds)
                ->delete();
        }

        // Sync spesialisasi (single select — hanya 1 spesialisasi aktif)
        $user->courseTypes()->sync([$newTypeId]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Spesialisasi course berhasil dipilih!');
    }
}
