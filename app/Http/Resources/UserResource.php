<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'username'         => $this->username,
            'email'            => $this->email,
            'role'             => $this->role,
            'is_active'        => $this->is_active,
            'last_login_at'    => $this->last_login_at?->toDateTimeString(),
            'course_types'     => CourseTypeResource::collection($this->whenLoaded('courseTypes')),
            'quiz_attempts_count' => $this->whenCounted('quizAttempts'),
            'created_at'       => $this->created_at?->toDateTimeString(),
        ];
    }
}
