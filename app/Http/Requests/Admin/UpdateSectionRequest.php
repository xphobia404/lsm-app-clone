<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isAdmin() ?? false; }

    public function rules(): array
    {
        return [
            'course_type_id'            => 'nullable|exists:course_types,id',
            'title'                     => 'required|string|max:255',
            'description'               => 'nullable|string',
            'thumbnail'                 => 'nullable|image|max:5120',
            'passing_score'             => 'nullable|integer|min:0|max:100',
            'order'                     => 'nullable|integer|min:0',
            'is_published'              => 'boolean',
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
            'pages.*.youtube_url'       => 'nullable|url',
            'pages.*.drive_url'         => 'nullable|url',
            'pages.*.new_image'         => 'nullable|file|image|max:5120',
            'pages.*.new_video'         => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:204800',
            'pages.*.new_audio'         => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac|max:51200',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Judul section wajib diisi.',
            'thumbnail.image'   => 'Thumbnail harus berupa gambar.',
            'thumbnail.max'     => 'Ukuran thumbnail maksimal 5MB.',
            'passing_score.max' => 'Passing score maksimal 100.',
        ];
    }
}
