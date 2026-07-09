<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('username', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $allSchemas = LearningSchema::active()->orderBy('title')->get();

        return view('admin.users.create', compact('allSchemas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role' => 'required|in:admin,user',
            'is_active' => 'sometimes|boolean',
            'schema_ids' => 'nullable|array',
            'schema_ids.*' => 'integer|exists:learning_schemas,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($validated['schema_ids'])) {
            $pivot = [];
            foreach ($validated['schema_ids'] as $id) {
                $pivot[$id] = ['enrolled_at' => now(), 'status' => 'active'];
            }
            $user->learningSchemas()->sync($pivot);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->loadCount(['progresses', 'quizAttempts']);

        $user->load([
            'learningSchemas' => fn ($q) => $q
                ->withCount('sections')
                ->with([
                    'sections' => fn ($sectionQuery) => $sectionQuery
                        ->select('sections.id', 'sections.title', 'sections.is_active')
                        ->orderByPivot('section_order')
                        ->orderBy('sections.id'),
                ]),
            'progresses' => fn ($q) => $q
                ->with('section:id,title')
                ->latest('updated_at')
                ->limit(5),
        ]);

        $progressMap = $user->progresses()
            ->get()
            ->keyBy('section_id');

        $sectionsWithStatus = $user->learningSchemas
            ->flatMap(function ($schema) use ($progressMap) {
                return $schema->sections->map(function ($section) use ($schema, $progressMap) {
                    $progress = $progressMap->get($section->id);

                    $section->schema_title = $schema->title;
                    $section->progress_status = ($progress && $progress->status === 'completed')
                        ? 'completed'
                        : 'not_completed';

                    return $section;
                });
            })
            ->values();

        $totalSections = $sectionsWithStatus->count();

        $completedSections = $sectionsWithStatus
            ->where('progress_status', 'completed')
            ->count();

        return view('admin.users.show', compact(
            'user',
            'sectionsWithStatus',
            'totalSections',
            'completedSections'
        ));
    }

    public function edit(User $user)
    {
        $allSchemas = LearningSchema::active()->orderBy('title')->get();
        $enrolledIds = $user->learningSchemas()->pluck('learning_schema_id')->toArray();
        $enrollments = $user->learningSchemas()->get();

        return view('admin.users.edit', compact('user', 'allSchemas', 'enrolledIds', 'enrollments'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$user->id.'|alpha_dash',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', Password::min(8)],
            'role' => 'required|in:admin,user',
            'is_active' => 'sometimes|boolean',
            'schema_ids' => 'nullable|array',
            'schema_ids.*' => 'integer|exists:learning_schemas,id',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        $schemaIds = $validated['schema_ids'] ?? [];
        $existing = $user->learningSchemas()->pluck('learning_schema_id')->toArray();
        $pivot = [];
        foreach ($schemaIds as $id) {
            $pivot[$id] = in_array($id, $existing)
                ? []
                : ['enrolled_at' => now(), 'status' => 'active'];
        }
        $user->learningSchemas()->sync($pivot);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Tidak bisa menghapus akun sendiri.');
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function toggleActive(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Tidak bisa menonaktifkan akun sendiri.');
        $user->update(['is_active' => ! $user->is_active]);
        $label = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "User berhasil {$label}.");
    }
}
