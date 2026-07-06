<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user()?->isAdmin();

        return [
            'id'             => $this->id,
            'question'       => $this->question,
            'option_a'       => $this->option_a,
            'option_b'       => $this->option_b,
            'option_c'       => $this->option_c,
            'option_d'       => $this->option_d,
            // Jawaban benar & penjelasan hanya dikembalikan ke admin
            'correct_answer' => $this->when($isAdmin, $this->correct_answer),
            'explanation'    => $this->when($isAdmin, $this->explanation),
            'order'          => $this->order,
            'media'          => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
