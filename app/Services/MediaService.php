<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    /**
     * Disk storage yang dipakai (default: public).
     * Bisa diubah via config atau override di constructor.
     */
    public function __construct(
        protected string $disk = 'public'
    ) {}

    // ── ATTACH ────────────────────────────────────────────────────

    /**
     * Upload file dan attach sebagai media ke model.
     *
     * @param  Model        $mediable   Model target (Section, Content, Quiz, dll)
     * @param  UploadedFile $file       File dari request
     * @param  string       $mediaType  'image' | 'video' | 'audio' | 'url'
     * @param  array        $extra      Override kolom tambahan (title, description, media_order)
     * @return Media
     */
    public function attachFile(
        Model $mediable,
        UploadedFile $file,
        string $mediaType = 'image',
        array $extra = []
    ): Media {
        $folder   = $this->resolveFolder($mediable);
        $filePath = $file->store($folder, $this->disk);

        return $mediable->media()->create(array_merge([
            'media_type'  => $mediaType,
            'title'       => $extra['title'] ?? $file->getClientOriginalName(),
            'description' => $extra['description'] ?? null,
            'file_path'   => $filePath,
            'url'         => null,
            'media_order' => $extra['media_order'] ?? $this->nextOrder($mediable),
            'is_active'   => $extra['is_active'] ?? true,
        ], $extra));
    }

    /**
     * Attach media berupa URL eksternal (YouTube, Google Drive, link gambar, dll).
     *
     * @param  Model  $mediable
     * @param  string $url
     * @param  string $mediaType  'image' | 'video' | 'audio' | 'url'
     * @param  array  $extra
     * @return Media
     */
    public function attachUrl(
        Model $mediable,
        string $url,
        string $mediaType = 'url',
        array $extra = []
    ): Media {
        return $mediable->media()->create(array_merge([
            'media_type'  => $mediaType,
            'title'       => $extra['title'] ?? null,
            'description' => $extra['description'] ?? null,
            'file_path'   => null,
            'url'         => $url,
            'media_order' => $extra['media_order'] ?? $this->nextOrder($mediable),
            'is_active'   => $extra['is_active'] ?? true,
        ], $extra));
    }

    /**
     * Attach dari request otomatis — deteksi apakah input berupa file atau URL.
     *
     * Contoh pemakaian di controller:
     *   $mediaService->attachFromRequest($section, $request, 'attachment');
     *
     * @param  Model                    $mediable
     * @param  \Illuminate\Http\Request $request
     * @param  string                   $fileKey    Nama input file di form
     * @param  string                   $urlKey     Nama input URL di form
     * @param  array                    $extra
     * @return Media|null
     */
    public function attachFromRequest(
        Model $mediable,
        \Illuminate\Http\Request $request,
        string $fileKey = 'file',
        string $urlKey  = 'url',
        array  $extra   = []
    ): ?Media {
        if ($request->hasFile($fileKey)) {
            $mediaType = $this->guessMediaTypeFromFile($request->file($fileKey));
            return $this->attachFile($mediable, $request->file($fileKey), $mediaType, $extra);
        }

        if ($request->filled($urlKey)) {
            $mediaType = $extra['media_type'] ?? 'url';
            return $this->attachUrl($mediable, $request->input($urlKey), $mediaType, $extra);
        }

        return null;
    }

    // ── DETACH / DELETE ────────────────────────────────────────────

    /**
     * Hapus media record dan file fisiknya dari storage.
     */
    public function delete(Media $media): void
    {
        if ($media->file_path && Storage::disk($this->disk)->exists($media->file_path)) {
            Storage::disk($this->disk)->delete($media->file_path);
        }

        $media->delete();
    }

    /**
     * Hapus semua media milik sebuah model (beserta file fisik).
     */
    public function deleteAll(Model $mediable): void
    {
        $mediable->media()->each(fn (Media $media) => $this->delete($media));
    }

    // ── UPDATE ────────────────────────────────────────────────────

    /**
     * Update metadata media (title, description, is_active).
     * Jika ada file baru dikirim, file lama dihapus dan diganti.
     */
    public function update(
        Media $media,
        array $data,
        ?UploadedFile $newFile = null
    ): Media {
        if ($newFile) {
            // Hapus file lama
            if ($media->file_path && Storage::disk($this->disk)->exists($media->file_path)) {
                Storage::disk($this->disk)->delete($media->file_path);
            }

            $folder            = $this->resolveFolderFromType($media->mediable_type);
            $data['file_path'] = $newFile->store($folder, $this->disk);
            $data['media_type'] = $this->guessMediaTypeFromFile($newFile);
        }

        $media->update($data);
        return $media->fresh();
    }

    // ── REORDER ──────────────────────────────────────────────────

    /**
     * Urutkan ulang media berdasarkan array of IDs.
     *
     * Contoh:
     *   $mediaService->reorder($section, [3, 1, 2]);
     *   // media id=3 → order 1, id=1 → order 2, id=2 → order 3
     */
    public function reorder(Model $mediable, array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            $mediable->media()
                ->where('id', $id)
                ->update(['media_order' => $order + 1]);
        }
    }

    // ── SYNC (bulk replace) ─────────────────────────────────────────

    /**
     * Sync media dari request — hapus media yang tidak ada di $keepIds,
     * lalu upload file baru yang dikirim.
     *
     * Cocok untuk form edit yang menampilkan media existing + upload baru.
     *
     * @param  Model                    $mediable
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $keepIds    ID media yang tetap disimpan
     * @param  string                   $fileKey    Input name untuk file baru (bisa array)
     */
    public function syncFromRequest(
        Model $mediable,
        \Illuminate\Http\Request $request,
        array $keepIds  = [],
        string $fileKey = 'new_files'
    ): void {
        // Hapus media yang tidak ada di keepIds
        $mediable->media()
            ->when(! empty($keepIds), fn ($q) => $q->whereNotIn('id', $keepIds))
            ->when(empty($keepIds), fn ($q) => $q) // hapus semua jika keepIds kosong
            ->each(fn (Media $m) => $this->delete($m));

        // Upload file baru jika ada
        if ($request->hasFile($fileKey)) {
            $files = $request->file($fileKey);
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                $mediaType = $this->guessMediaTypeFromFile($file);
                $this->attachFile($mediable, $file, $mediaType);
            }
        }
    }

    // ── PRIVATE HELPERS ────────────────────────────────────────────

    /**
     * Tentukan folder penyimpanan berdasarkan jenis model.
     */
    protected function resolveFolder(Model $mediable): string
    {
        return $this->resolveFolderFromType(get_class($mediable));
    }

    protected function resolveFolderFromType(string $modelClass): string
    {
        return match (true) {
            str_ends_with($modelClass, 'Section') => 'media/sections',
            str_ends_with($modelClass, 'Content') => 'media/contents',
            str_ends_with($modelClass, 'Quiz')    => 'media/quizzes',
            default                               => 'media/misc',
        };
    }

    /**
     * Ambil nilai media_order berikutnya untuk model tertentu.
     */
    protected function nextOrder(Model $mediable): int
    {
        return ((int) $mediable->media()->max('media_order')) + 1;
    }

    /**
     * Tebak media_type dari ekstensi file yang diupload.
     */
    protected function guessMediaTypeFromFile(UploadedFile $file): string
    {
        $mime = $file->getMimeType() ?? '';

        if (str_starts_with($mime, 'image/'))  return 'image';
        if (str_starts_with($mime, 'video/'))  return 'video';
        if (str_starts_with($mime, 'audio/'))  return 'audio';

        return 'url'; // fallback untuk file lain (PDF, dll)
    }
}
