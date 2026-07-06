<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isAdmin() ?? false; }

    public function rules(): array
    {
        return [
            // ── Kolom utama sections ──────────────────────────────────────────
            'course_type_id'            => 'nullable|exists:course_types,id',
            'title'                     => 'required|string|max:255',
            'description'               => 'nullable|string|max:1000',
            'thumbnail'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'passing_score'             => 'nullable|integer|min:0|max:100',
            'order'                     => 'nullable|integer|min:0',
            'is_published'              => 'boolean',

            // ── Pages (JSON slides) ───────────────────────────────────────────
            'pages'                     => 'nullable|array',
            'pages.*.title'             => 'nullable|string|max:255',
            'pages.*.content'           => 'nullable|string',
            'pages.*.slide_media_type'  => 'nullable|string|in:none,image,video_upload,audio,youtube,drive',
            'pages.*.image_url'         => 'nullable|string',
            'pages.*.image_path'        => 'nullable|string',
            'pages.*.video_url'         => 'nullable|string',
            'pages.*.video_path'        => 'nullable|string',
            'pages.*.audio_url'         => 'nullable|string',
            'pages.*.audio_path'        => 'nullable|string',
            'pages.*.youtube_url'       => 'nullable|url|max:500',
            'pages.*.drive_url'         => 'nullable|url|max:500',

            // ── Upload file per slide ─────────────────────────────────────────
            'pages.*.new_image'         => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'pages.*.new_video'         => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:204800',
            'pages.*.new_audio'         => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac|max:51200',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Judul section wajib diisi.',
            'title.max'                 => 'Judul section maksimal 255 karakter.',
            'description.max'           => 'Deskripsi maksimal 1000 karakter.',
            'thumbnail.image'           => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes'           => 'Format thumbnail: jpg, png, atau webp.',
            'thumbnail.max'             => 'Ukuran thumbnail maksimal 2MB.',
            'passing_score.min'         => 'Passing score minimal 0.',
            'passing_score.max'         => 'Passing score maksimal 100.',
            'course_type_id.exists'     => 'Spesialisasi course tidak valid.',
            'pages.*.youtube_url.url'   => 'Format YouTube URL tidak valid.',
            'pages.*.drive_url.url'     => 'Format Google Drive URL tidak valid.',
            'pages.*.new_image.image'   => 'File gambar slide tidak valid.',
            'pages.*.new_image.max'     => 'Ukuran gambar slide maksimal 5MB.',
            'pages.*.new_video.max'     => 'Ukuran video slide maksimal 200MB.',
            'pages.*.new_audio.max'     => 'Ukuran audio slide maksimal 50MB.',
        ];
    }
}
