<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use Illuminate\Http\Request;

class CourseTypeController extends Controller
{
    public function index()
    {
        $courseTypes = CourseType::withCount(['sections', 'users'])
            ->orderBy('order')
            ->get();

        return view('admin.course-types.index', compact('courseTypes'));
    }

    public function create()
    {
        return view('admin.course-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['order'] = $data['order'] ?? (CourseType::max('order') + 1);

        CourseType::create($data);

        return redirect()->route('admin.course-types.index')
            ->with('success', 'Spesialisasi course berhasil dibuat.');
    }

    public function edit(CourseType $courseType)
    {
        return view('admin.course-types.edit', compact('courseType'));
    }

    public function update(Request $request, CourseType $courseType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $courseType->update($data);

        return redirect()->route('admin.course-types.index')
            ->with('success', 'Spesialisasi course berhasil diperbarui.');
    }

    public function destroy(CourseType $courseType)
    {
        // Lepas relasi pivot user sebelum hapus
        $courseType->users()->detach();

        // Set course_type_id sections ke null sebelum hapus
        $courseType->sections()->update(['course_type_id' => null]);

        $courseType->delete();

        return redirect()->route('admin.course-types.index')
            ->with('success', 'Spesialisasi course berhasil dihapus.');
    }

    /**
     * Toggle show / hide (is_active) course type.
     */
    public function toggleActive(CourseType $courseType)
    {
        $courseType->update(['is_active' => ! $courseType->is_active]);

        $msg = $courseType->is_active
            ? 'Spesialisasi berhasil ditampilkan.'
            : 'Spesialisasi berhasil disembunyikan.';

        return back()->with('success', $msg);
    }
}
