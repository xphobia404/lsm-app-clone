<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\User;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /** Halaman kelola enrollment per user */
    public function edit(User $user)
    {
        $allSchemas = LearningSchema::active()->orderBy('title')->get();
        $enrolledIds = $user->learningSchemas()->pluck('learning_schema_id')->toArray();
        $enrollments = $user->learningSchemas()->get(); // dengan pivot

        return view('admin.users.enrollment', compact('user', 'allSchemas', 'enrolledIds', 'enrollments'));
    }

    /** Simpan perubahan enrollment (sync) */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'schema_ids'   => 'nullable|array',
            'schema_ids.*' => 'integer|exists:learning_schemas,id',
        ]);

        $schemaIds = $request->input('schema_ids', []);

        // Buat payload pivot untuk setiap schema yang dipilih
        $syncData = [];
        foreach ($schemaIds as $id) {
            // Jika sudah ada enrollment-nya, jangan overwrite enrolled_at
            if ($user->isEnrolledIn((int) $id)) {
                $syncData[$id] = []; // keep existing pivot
            } else {
                $syncData[$id] = ['enrolled_at' => now(), 'status' => 'active'];
            }
        }

        $user->learningSchemas()->sync($syncData);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Enrollment berhasil diperbarui.');
    }

    /** Enroll cepat (1 schema) tanpa halaman form */
    public function enroll(Request $request, User $user)
    {
        $request->validate(['learning_schema_id' => 'required|integer|exists:learning_schemas,id']);
        $user->enroll((int) $request->learning_schema_id);
        return back()->with('success', 'User berhasil di-enroll.');
    }

    /** Drop enrollment */
    public function drop(Request $request, User $user)
    {
        $request->validate(['learning_schema_id' => 'required|integer|exists:learning_schemas,id']);
        $user->unenroll((int) $request->learning_schema_id);
        return back()->with('success', 'Enrollment berhasil dihapus.');
    }

    /** Update status enrollment (active / completed / dropped) */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'learning_schema_id' => 'required|integer|exists:learning_schemas,id',
            'status'             => 'required|in:active,completed,dropped',
        ]);
        $user->updateEnrollmentStatus((int) $request->learning_schema_id, $request->status);
        return back()->with('success', 'Status enrollment diperbarui.');
    }
}
