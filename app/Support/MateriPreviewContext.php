<?php

namespace App\Support;

use App\Models\LearningSchema;
use App\Models\Section;

class MateriPreviewContext
{
    /**
     * @return array{
     *     previewExitUrl: string,
     *     schemaShowUrl: string,
     *     sectionShowUrl: callable(Section): string,
     *     quizIndexUrl: callable(Section): string,
     *     quizSubmitUrl: callable(Section): string,
     * }
     */
    public static function variables(?LearningSchema $learningSchema, bool $adminPreview = false): array
    {
        if ($adminPreview && $learningSchema !== null) {
            return [
                'previewExitUrl' => route('admin.learning-schemas.index'),
                'schemaShowUrl' => route('admin.learning-schemas.preview.show', $learningSchema),
                'sectionShowUrl' => fn (Section $targetSection) => route('admin.learning-schemas.preview.section', [$learningSchema, $targetSection]),
                'quizIndexUrl' => fn (Section $targetSection) => route('admin.learning-schemas.preview.quizzes', [$learningSchema, $targetSection]),
                'quizSubmitUrl' => fn (Section $targetSection) => route('admin.learning-schemas.preview.quizzes.submit', [$learningSchema, $targetSection]),
            ];
        }

        $schemaShowUrl = $learningSchema !== null
            ? route('user.schemas.show', $learningSchema)
            : route('user.schemas.index');

        return [
            'previewExitUrl' => route('user.schemas.index'),
            'schemaShowUrl' => $schemaShowUrl,
            'sectionShowUrl' => fn (Section $targetSection) => route('user.sections.show', [$learningSchema, $targetSection]),
            'quizIndexUrl' => fn (Section $targetSection) => route('user.quizzes.index', $targetSection),
            'quizSubmitUrl' => fn (Section $targetSection) => route('user.quizzes.submit', $targetSection),
        ];
    }
}
