<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use Illuminate\Http\Request;

class CourseTypeController extends Controller
{
    /**
     * Tampilkan halaman pemilihan spesialisasi.
     */
    public function select()
    {
        $courseTypes = CourseType::active()->withCount('sections')->get();
        return view('user.course-type.select', compact('courseTypes'));
    }

    /**
     * Simpan pilihan spesialisasi user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_type_id' => 'required|exists:course_types,id',
        ]);

        $user = auth()->user();
        $newTypeId = $request->course_type_id;

        // Jika user ganti spesialisasi, reset progress lama
        if ($user->course_type_id && $user->course_type_id !== (int) $newTypeId) {
            $user->progress()->delete();
            $user->quizAttempts()->delete();
        }

        $user->update(['course_type_id' => $newTypeId]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Spesialisasi course berhasil dipilih!');
    }
}
