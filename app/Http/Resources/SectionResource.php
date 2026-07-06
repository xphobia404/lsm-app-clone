<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'thumbnail_url' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'passing_score' => $this->passing_score,
            'order'         => $this->order,
            'is_published'  => $this->is_published,
            'course_type'   => new CourseTypeResource($this->whenLoaded('courseType')),
            'quizzes_count' => $this->whenCounted('quizzes'),
            'pages_count'   => is_array($this->pages) ? count($this->pages) : 0,
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
