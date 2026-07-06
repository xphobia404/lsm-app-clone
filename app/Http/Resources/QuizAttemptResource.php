<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'section_id'     => $this->section_id,
            'attempt_number' => $this->attempt_number,
            'score'          => $this->score,
            'score_percent'  => $this->score_percent,
            'passed'         => $this->passed,
            'submitted_at'   => $this->submitted_at?->toDateTimeString(),
            'section'        => new SectionResource($this->whenLoaded('section')),
            // Jawaban detail hanya untuk admin
            'answers'        => $this->when($request->user()?->isAdmin(), $this->answers),
        ];
    }
}
