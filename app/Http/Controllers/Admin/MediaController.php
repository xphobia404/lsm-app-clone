<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Quiz;
use App\Models\Section;
use App\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    // =========================================================================
    // Upload
    // =========================================================================

    /**
     * Upload banyak file untuk Quiz.
     * POST /admin/sections/{section}/quizzes/{quiz}/media
     */
    public function storeForQuiz(Request $request, Section $section, Quiz $quiz)
    {
        $request->validate([
            'files'   => 'required|array|min:1|max:10',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,mp3,wav,ogg|max:51200',
        ], [
            'files.required' => 'Pilih minimal 1 file.',
            'files.*.mimes'  => 'Format file tidak didukung.',
            'files.*.max'    => 'Ukuran file maksimal 50MB.',
        ]);

        $this->mediaService->uploadMany(
            $quiz,
            $request->file('files'),
            'media/quizzes/' . $quiz->id
        );

        return back()->with('success', 'Media berhasil diupload.');
    }

    /**
     * Upload banyak file untuk Section.
     * POST /admin/sections/{section}/media
     */
    public function storeForSection(Request $request, Section $section)
    {
        $request->validate([
            'files'   => 'required|array|min:1|max:10',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,mp3,wav,ogg|max:51200',
        ], [
            'files.required' => 'Pilih minimal 1 file.',
            'files.*.mimes'  => 'Format file tidak didukung.',
            'files.*.max'    => 'Ukuran file maksimal 50MB.',
        ]);

        $this->mediaService->uploadMany(
            $section,
            $request->file('files'),
            'media/sections/' . $section->id
        );

        return back()->with('success', 'Media berhasil diupload.');
    }

    // =========================================================================
    // Destroy
    // =========================================================================

    /**
     * Hapus satu media.
     * DELETE /admin/media/{media}
     */
    public function destroy(Media $media)
    {
        $this->mediaService->deleteMedia($media);

        return back()->with('success', 'Media berhasil dihapus.');
    }

    // =========================================================================
    // Reorder
    // =========================================================================

    /**
     * Reorder media via AJAX.
     * PUT /admin/media/reorder
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders'   => 'required|array',
            'orders.*' => 'integer|exists:media,id',
        ]);

        $this->mediaService->reorder($request->input('orders'));

        return response()->json(['success' => true]);
    }
}
