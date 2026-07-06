<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * MediaService
 *
 * Satu service terpusat untuk semua operasi upload, replace, dan hapus media.
 * Bisa dipakai oleh controller manapun yang butuh upload media.
 *
 * Contoh pemakaian:
 *   app(MediaService::class)->uploadMany($quiz, $request->file('files'), 'media/quizzes');
 *   app(MediaService::class)->uploadSingle($section, $request->file('thumbnail'), 'sections/thumbnails');
 *   app(MediaService::class)->replace($section, 'thumbnail', $request->file('thumbnail'), 'sections/thumbnails');
 *   app(MediaService::class)->deletePath('sections/thumbnails/file.jpg');
 */
class MediaService
{
    // =========================================================================
    // A. Polymorphic Media Table (tabel `media`)
    // =========================================================================

    /**
     * Upload SATU file dan simpan ke tabel `media` (polymorphic).
     *
     * @param  Model         $model    Model pemilik (Quiz, Section, dll)
     * @param  UploadedFile  $file     File yang diupload
     * @param  string        $folder   Folder tujuan di storage/public (e.g. 'media/quizzes')
     * @param  string        $disk     Default: 'public'
     * @param  int|null      $order    Urutan tampil; null = auto-increment
     * @return Media
     */
    public function uploadOne(
        Model $model,
        UploadedFile $file,
        string $folder,
        string $disk = 'public',
        ?int $order = null
    ): Media {
        $mime = $file->getMimeType();
        $path = $file->store($folder, $disk);

        if ($order === null) {
            $order = ($model->media()->max('order') ?? -1) + 1;
        }

        return $model->media()->create([
            'type'          => $this->detectType($mime),
            'disk'          => $disk,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $mime,
            'size'          => $file->getSize(),
            'order'         => $order,
        ]);
    }

    /**
     * Upload BANYAK file sekaligus ke tabel `media` (polymorphic).
     *
     * @param  Model           $model
     * @param  UploadedFile[]  $files
     * @param  string          $folder
     * @param  string          $disk
     * @return Media[]
     */
    public function uploadMany(
        Model $model,
        array $files,
        string $folder,
        string $disk = 'public'
    ): array {
        $lastOrder = $model->media()->max('order') ?? -1;
        $result    = [];

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }
            $result[] = $this->uploadOne($model, $file, $folder, $disk, ++$lastOrder);
        }

        return $result;
    }

    /**
     * Hapus satu record Media beserta file fisiknya.
     *
     * @param  Media  $media
     * @return void
     */
    public function deleteMedia(Media $media): void
    {
        $media->delete(); // Model::booted() otomatis hapus file fisik
    }

    /**
     * Hapus SEMUA media milik sebuah model (polymorphic).
     *
     * @param  Model  $model
     * @return void
     */
    public function deleteAllMedia(Model $model): void
    {
        $model->media()->each(fn (Media $m) => $m->delete());
    }

    /**
     * Update urutan media (untuk drag-drop reorder).
     *
     * @param  array  $orders  [order_index => media_id, ...]
     * @return void
     */
    public function reorder(array $orders): void
    {
        foreach ($orders as $order => $id) {
            Media::where('id', $id)->update(['order' => (int) $order]);
        }
    }

    // =========================================================================
    // B. File Storage Langsung (BUKAN tabel media)
    //    Untuk thumbnail, video_file, audio_file, page images, dll
    //    yang disimpan sebagai path string di kolom tabel utama.
    // =========================================================================

    /**
     * Upload SATU file ke storage dan kembalikan path-nya (string).
     * Tidak menyentuh tabel `media`.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder
     * @param  string        $disk
     * @return string         Path relatif di storage disk
     */
    public function storeDirect(
        UploadedFile $file,
        string $folder,
        string $disk = 'public'
    ): string {
        return $file->store($folder, $disk);
    }

    /**
     * Replace file lama dengan file baru di storage langsung.
     * Hapus file lama (jika ada), upload file baru, kembalikan path baru.
     *
     * @param  UploadedFile  $newFile
     * @param  string        $folder
     * @param  string|null   $oldPath   Path lama yang akan dihapus
     * @param  string        $disk
     * @return string         Path baru
     */
    public function replaceDirect(
        UploadedFile $newFile,
        string $folder,
        ?string $oldPath = null,
        string $disk = 'public'
    ): string {
        $this->deletePath($oldPath, $disk);
        return $this->storeDirect($newFile, $folder, $disk);
    }

    /**
     * Hapus file dari storage berdasarkan path string.
     * Aman dipanggil dengan null atau path kosong.
     *
     * @param  string|null  $path
     * @param  string       $disk
     * @return void
     */
    public function deletePath(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Hapus banyak path sekaligus.
     *
     * @param  array   $paths  [string|null, ...]
     * @param  string  $disk
     * @return void
     */
    public function deletePaths(array $paths, string $disk = 'public'): void
    {
        foreach ($paths as $path) {
            $this->deletePath($path, $disk);
        }
    }

    // =========================================================================
    // C. Helper Privat
    // =========================================================================

    /**
     * Deteksi tipe media dari MIME type.
     */
    private function detectType(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'video/')) return 'video';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        return 'image';
    }

    /**
     * Buat URL publik dari path storage.
     */
    public function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }
}
