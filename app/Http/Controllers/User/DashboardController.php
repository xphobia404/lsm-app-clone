<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\Section;
use App\Models\UserProgress;
use App\Models\QuizAttempt;

class DashboardController extends Controller
{
    private function getUserCourseData(): array
    {
        $user = auth()->user();

        if (!$user->hasSelectedCourseType()) {
            return [
                'courseTypes'    => collect(),
                'sectionsMap'    => collect(),
                'progressMap'    => collect(),
                'attemptsMap'    => collect(),
                'completedCount' => 0,
                'totalCount'     => 0,
                'needSelectType' => true,
            ];
        }

        // Ambil spesialisasi user beserta sections-nya (ordered)
        $courseTypeIds = $user->courseTypes()->pluck('course_types.id');

        $courseTypes = CourseType::whereIn('id', $courseTypeIds)
            ->orderBy('order')
            ->get();

        $sections = Section::where('is_published', true)
            ->whereIn('course_type_id', $courseTypeIds)
            ->orderBy('course_type_id')
            ->orderBy('order')
            ->get();

        // Group sections by course_type_id
        $sectionsMap = $sections->groupBy('course_type_id');

        if ($sections->isEmpty()) {
            return [
                'courseTypes'    => $courseTypes,
                'sectionsMap'    => collect(),
                'progressMap'    => collect(),
                'attemptsMap'    => collect(),
                'completedCount' => 0,
                'totalCount'     => 0,
                'needSelectType' => false,
            ];
        }

        // Unlock section pertama tiap spesialisasi
        foreach ($courseTypes as $ct) {
            $first = $sectionsMap->get($ct->id)?->first();
            if ($first) {
                UserProgress::firstOrCreate(
                    ['user_id' => $user->id, 'section_id' => $first->id],
                    ['unlocked' => true, 'status' => 'not_started']
                );
            }
        }

        $progressMap = UserProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('section_id');

        $attemptsMap = QuizAttempt::where('user_id', $user->id)
            ->selectRaw('
                section_id,
                COUNT(*)               AS total_attempts,
                MAX(score)             AS best_score,
                MAX(CASE WHEN passed = true THEN 1 ELSE 0 END) AS ever_passed
            ')
            ->groupBy('section_id')
            ->get()
            ->keyBy('section_id');

        $completedCount = $progressMap->where('status', 'completed')->count();
        $totalCount     = $sections->count();

        return compact('courseTypes', 'sectionsMap', 'progressMap', 'attemptsMap', 'completedCount', 'totalCount')
            + ['needSelectType' => false];
    }

    public function index()
    {
        $data = $this->getUserCourseData();

        if ($data['needSelectType']) {
            return redirect()->route('user.course-type.select');
        }

        return view('user.dashboard', $data);
    }

    public function courses()
    {
        $data = $this->getUserCourseData();

        if ($data['needSelectType']) {
            return redirect()->route('user.course-type.select');
        }

        return view('user.courses', $data);
    }
}
