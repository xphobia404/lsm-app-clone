<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\ResetProgressRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\CourseType;
use App\Models\Section;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // =========================================================================
    // Index
    // =========================================================================

    public function index(\Illuminate\Http\Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25])
            ? (int) $request->input('per_page')
            : 10;

        $query = User::with('courseTypes')
            ->withCount('quizAttempts')
            ->where('role', 'user');

        if ($q = $request->input('q')) {
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', '%' . $q . '%')
                   ->orWhere('username', 'like', '%' . $q . '%')
                   ->orWhere('email', 'like', '%' . $q . '%');
            });
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        if ($ct = $request->input('course_type_id')) {
            $query->whereHas('courseTypes', fn ($sq) => $sq->where('course_types.id', $ct));
        }

        $users       = $query->latest()->paginate($perPage)->withQueryString();
        $courseTypes = CourseType::active()->orderBy('order')->get();

        return view('admin.users.index', compact('users', 'courseTypes'));
    }

    // =========================================================================
    // Create / Store
    // =========================================================================

    public function create()
    {
        $courseTypes = CourseType::active()->orderBy('order')->get();
        return view('admin.users.create', compact('courseTypes'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $data['role']     = 'user';
        $data['password'] = Hash::make($data['password']);

        $user = User::create(collect($data)->except('course_type_ids')->toArray());

        if (! empty($data['course_type_ids'])) {
            $user->courseTypes()->sync($data['course_type_ids']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    // =========================================================================
    // Show
    // =========================================================================

    public function show(User $user)
    {
        $user->load('courseTypes');

        $courseTypeIds = $user->courseTypes->pluck('id');

        $sections = $courseTypeIds->isNotEmpty()
            ? Section::whereIn('course_type_id', $courseTypeIds)->orderBy('course_type_id')->orderBy('order')->get()
            : Section::orderBy('order')->get();

        $progress = UserProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('section_id');

        $attemptsPerSection = QuizAttempt::where('user_id', $user->id)
            ->selectRaw('
                section_id,
                COUNT(*)                                       AS total_attempts,
                MAX(score_percent)                             AS best_score,
                MAX(CASE WHEN passed = true THEN 1 ELSE 0 END) AS ever_passed
            ')
            ->groupBy('section_id')
            ->get()
            ->keyBy('section_id');

        return view('admin.users.show', compact(
            'user', 'sections', 'progress', 'attemptsPerSection'
        ));
    }

    // =========================================================================
    // Edit / Update
    // =========================================================================

    public function edit(User $user)
    {
        $courseTypes     = CourseType::active()->orderBy('order')->get();
        $selectedTypeIds = $user->courseTypes()->pluck('course_types.id')->toArray();

        return view('admin.users.edit', compact('user', 'courseTypes', 'selectedTypeIds'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->update(collect($data)->except('course_type_ids')->toArray());
        $user->courseTypes()->sync($data['course_type_ids'] ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    // =========================================================================
    // Destroy
    // =========================================================================

    public function destroy(User $user)
    {
        $user->courseTypes()->detach();
        $user->progresses()->delete();
        $user->quizAttempts()->delete();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // =========================================================================
    // Actions
    // =========================================================================

    public function resetPassword(ResetPasswordRequest $request, User $user)
    {
        $user->update(['password' => Hash::make($request->validated('password'))]);

        return back()->with('success', 'Password berhasil direset.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => ! $user->is_active]);

        $msg = $user->is_active ? 'User berhasil diaktifkan.' : 'User berhasil dinonaktifkan.';

        return back()->with('success', $msg);
    }

    /**
     * Reset seluruh progress & quiz attempts user untuk course type tertentu.
     * Route: POST /admin/users/{user}/reset-progress
     */
    public function resetProgress(ResetProgressRequest $request, User $user)
    {
        $courseTypeId = $request->validated('course_type_id');

        $sectionIds = Section::where('course_type_id', $courseTypeId)->pluck('id');

        UserProgress::where('user_id', $user->id)
            ->whereIn('section_id', $sectionIds)
            ->delete();

        QuizAttempt::where('user_id', $user->id)
            ->whereIn('section_id', $sectionIds)
            ->delete();

        $courseTypeName = CourseType::find($courseTypeId)?->name ?? 'Course Type';

        return back()->with('success', "Progress user untuk spesialisasi '{$courseTypeName}' berhasil direset.");
    }
}
