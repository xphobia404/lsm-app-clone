<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'icon'           => $this->icon,
            'is_active'      => $this->is_active,
            'order'          => $this->order,
            'sections_count' => $this->whenCounted('sections'),
            'users_count'    => $this->whenCounted('users'),
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
