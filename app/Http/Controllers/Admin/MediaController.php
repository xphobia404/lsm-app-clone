<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Quiz;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Mapping tipe MIME ke enum type.
     */
    private function detectType(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'video/')) return 'video';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        return 'image';
    }

    /**
     * Upload satu atau banyak file untuk Quiz.
     * POST /admin/sections/{section}/quizzes/{quiz}/media
     */
    public function storeForQuiz(Request $request, Section $section, Quiz $quiz)
    {
        $request->validate([
            'files'   => 'required|array|min:1|max:10',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,mp3,wav,ogg|max:51200',
        ], [
            'files.required'   => 'Pilih minimal 1 file.',
            'files.*.mimes'    => 'Format file tidak didukung.',
            'files.*.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        $lastOrder = $quiz->media()->max('order') ?? -1;

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType();
            $path = $file->store('media/quizzes/' . $quiz->id, 'public');

            $quiz->media()->create([
                'type'          => $this->detectType($mime),
                'disk'          => 'public',
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $mime,
                'size'          => $file->getSize(),
                'order'         => ++$lastOrder,
            ]);
        }

        return back()->with('success', 'Media berhasil diupload.');
    }

    /**
     * Upload satu atau banyak file untuk Section.
     * POST /admin/sections/{section}/media
     */
    public function storeForSection(Request $request, Section $section)
    {
        $request->validate([
            'files'   => 'required|array|min:1|max:10',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,mp3,wav,ogg|max:51200',
        ], [
            'files.required'   => 'Pilih minimal 1 file.',
            'files.*.mimes'    => 'Format file tidak didukung.',
            'files.*.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        $lastOrder = $section->media()->max('order') ?? -1;

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType();
            $path = $file->store('media/sections/' . $section->id, 'public');

            $section->media()->create([
                'type'          => $this->detectType($mime),
                'disk'          => 'public',
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $mime,
                'size'          => $file->getSize(),
                'order'         => ++$lastOrder,
            ]);
        }

        return back()->with('success', 'Media berhasil diupload.');
    }

    /**
     * Hapus satu media.
     * DELETE /admin/media/{media}
     */
    public function destroy(Media $media)
    {
        $media->delete(); // otomatis hapus file fisik via Model::booted()
        return back()->with('success', 'Media berhasil dihapus.');
    }

    /**
     * Update urutan media (reorder via drag-drop / AJAX).
     * PUT /admin/media/reorder
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders'   => 'required|array',
            'orders.*' => 'integer|exists:media,id',
        ]);

        foreach ($request->orders as $order => $id) {
            Media::where('id', $id)->update(['order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}
