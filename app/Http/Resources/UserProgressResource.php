<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'section_id'      => $this->section_id,
            'status'          => $this->status,
            'unlocked'        => $this->unlocked,
            'completed_at'    => $this->completed_at?->toDateTimeString(),
            'quiz_passed_at'  => $this->quiz_passed_at?->toDateTimeString(),
            'section'         => new SectionResource($this->whenLoaded('section')),
        ];
    }
}
