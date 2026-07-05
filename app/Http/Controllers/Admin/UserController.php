<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\Section;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25])
            ? (int) $request->input('per_page')
            : 10;

        $query = User::with('courseTypes')->withCount('quizAttempts')->where('role', 'user');

        if ($q = $request->input('q')) {
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', '%' . $q . '%')
                   ->orWhere('username', 'like', '%' . $q . '%');
            });
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        if ($ct = $request->input('course_type_id')) {
            $query->whereHas('courseTypes', fn($sq) => $sq->where('course_types.id', $ct));
        }

        $users       = $query->latest()->paginate($perPage)->withQueryString();
        $courseTypes = CourseType::active()->orderBy('order')->get();

        return view('admin.users.index', compact('users', 'courseTypes'));
    }

    public function create()
    {
        $courseTypes = CourseType::active()->orderBy('order')->get();
        return view('admin.users.create', compact('courseTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'username'          => 'required|string|max:50|unique:users',
            'password'          => ['required', Password::min(6)],
            'course_type_ids'   => 'nullable|array',
            'course_type_ids.*' => 'exists:course_types,id',
        ]);

        $data['role']     = 'user';
        $data['password'] = Hash::make($data['password']);

        $user = User::create(collect($data)->except('course_type_ids')->toArray());

        if (!empty($data['course_type_ids'])) {
            $user->courseTypes()->sync($data['course_type_ids']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        $user->load('courseTypes');

        // Ambil sections berdasarkan spesialisasi user, atau semua jika tidak ada
        $courseTypeIds = $user->courseTypes->pluck('id');
        if ($courseTypeIds->isNotEmpty()) {
            $sections = Section::whereIn('course_type_id', $courseTypeIds)
                ->orderBy('order')
                ->get();
        } else {
            $sections = Section::orderBy('order')->get();
        }

        // Progress user, di-key by section_id
        $progress = UserProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('section_id');

        // Attempts per section — gunakan score_percent untuk best score
        $attemptsPerSection = QuizAttempt::where('user_id', $user->id)
            ->selectRaw('
                section_id,
                COUNT(*) as total_attempts,
                MAX(score_percent) as best_score,
                MAX(CASE WHEN passed = true THEN 1 ELSE 0 END) as ever_passed
            ')
            ->groupBy('section_id')
            ->get()
            ->keyBy('section_id');

        return view('admin.users.show', compact(
            'user', 'sections', 'progress', 'attemptsPerSection'
        ));
    }

    public function edit(User $user)
    {
        $courseTypes     = CourseType::active()->orderBy('order')->get();
        $selectedTypeIds = $user->courseTypes()->pluck('course_types.id')->toArray();
        return view('admin.users.edit', compact('user', 'courseTypes', 'selectedTypeIds'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'username'          => 'required|string|max:50|unique:users,username,' . $user->id,
            'course_type_ids'   => 'nullable|array',
            'course_type_ids.*' => 'exists:course_types,id',
        ]);

        $user->update(collect($data)->except('course_type_ids')->toArray());
        $user->courseTypes()->sync($data['course_type_ids'] ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', Password::min(6)],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil direset.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status user berhasil diubah.');
    }
}
