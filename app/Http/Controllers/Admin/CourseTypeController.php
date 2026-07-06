<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use Illuminate\Http\Request;
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
        // Auto-generate slug dari name + timestamp agar unik
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

    public function update(Request $request, CourseType $courseType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
            'order'       => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        // Regenerate slug hanya jika nama berubah
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
        // Lepas semua relasi pivot user
        $courseType->users()->detach();

        // Null-kan foreign key sections agar tidak cascade error
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
