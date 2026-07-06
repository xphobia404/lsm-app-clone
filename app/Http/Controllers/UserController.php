<?php

namespace App\Http\Controllers;

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
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
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
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:50|unique:users,username|alpha_dash',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => ['required', Password::min(8)],
            'role'      => 'required|in:admin,user',
            'is_active' => 'sometimes|boolean',
        ]);

        User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->loadCount(['progresses', 'quizAttempts']);
        $user->load([
            'progresses' => fn ($q) => $q->with('section:id,title')->latest('updated_at')->limit(5),
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:50|unique:users,username,' . $user->id . '|alpha_dash',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'  => ['nullable', Password::min(8)],
            'role'      => 'required|in:admin,user',
            'is_active' => 'sometimes|boolean',
        ]);

        $updateData = [
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri
        abort_if($user->id === auth()->id(), 403, 'Tidak bisa menghapus akun sendiri.');

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleActive(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Tidak bisa menonaktifkan akun sendiri.');

        $user->update(['is_active' => ! $user->is_active]);
        $label = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "User berhasil {$label}.");
    }
}
