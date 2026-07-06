<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseTypeRequest;
use App\Http\Requests\Admin\UpdateCourseTypeRequest;
use App\Models\CourseType;
use Illuminate\Support\Str;

class CourseTypeController extends Controller
{
    // =========================================================================
    // Index
    // =========================================================================

    public function index()
    {
        $courseTypes = CourseType::withCount(['sections', 'users'])
            ->orderBy('order')
            ->get();

        return view('admin.course-types.index', compact('courseTypes'));
    }

    // =========================================================================
    // Create / Store
    // =========================================================================

    public function create()
    {
        return view('admin.course-types.create');
    }

    public function store(StoreCourseTypeRequest $request)
    {
        $data = $request->validated();

        $data['order'] = $data['order'] ?? (CourseType::max('order') + 1);
        $data['slug']  = Str::slug($data['name']) . '-' . time();

        CourseType::create($data);

        return redirect()->route('admin.course-types.index')
            ->with('success', 'Spesialisasi course berhasil dibuat.');
    }

    // =========================================================================
    // Edit / Update
    // =========================================================================

    public function edit(CourseType $courseType)
    {
        return view('admin.course-types.edit', compact('courseType'));
    }

    public function update(UpdateCourseTypeRequest $request, CourseType $courseType)
    {
        $data = $request->validated();

        if ($data['name'] !== $courseType->name) {
            $data['slug'] = Str::slug($data['name']) . '-' . time();
        }

        $courseType->update($data);

        return redirect()->route('admin.course-types.index')
            ->with('success', 'Spesialisasi course berhasil diperbarui.');
    }

    // =========================================================================
    // Destroy
    // =========================================================================

    public function destroy(CourseType $courseType)
    {
        $courseType->users()->detach();
        $courseType->sections()->update(['course_type_id' => null]);
        $courseType->delete();

        return redirect()->route('admin.course-types.index')
            ->with('success', 'Spesialisasi course berhasil dihapus.');
    }

    // =========================================================================
    // Actions
    // =========================================================================

    public function toggleActive(CourseType $courseType)
    {
        $courseType->update(['is_active' => ! $courseType->is_active]);

        $msg = $courseType->is_active
            ? 'Spesialisasi berhasil ditampilkan.'
            : 'Spesialisasi berhasil disembunyikan.';

        return back()->with('success', $msg);
    }
}
