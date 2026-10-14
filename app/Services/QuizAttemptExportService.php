<?php

namespace App\Services;

use App\Models\QuizAttempt;
use App\Models\Section;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizAttemptExportService
{
    /**
     * Export quiz attempts to streamed CSV download.
     *
     * @param  Section|null  $section  Filter by section if provided
     * @param  array<string, mixed>  $filters
     */
    public function exportCsv(?Section $section = null, array $filters = []): StreamedResponse
    {
        $query = QuizAttempt::with([
            'user:id,name,username,email',
            'section:id,title,passing_score',
            'section.learningSchemas:id,title',
        ]);

        if ($section) {
            $query->where('section_id', $section->id);
        }

        $this->applyFilters($query, $filters);

        $query->latest('attempted_at');

        $dateSuffix = now()->format('Ymd_His');
        $filename = $section
            ? 'hasil_kuis_section_'.$section->id.'_'.$dateSuffix.'.csv'
            : 'hasil_kuis_semua_'.$dateSuffix.'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, [
                'No',
                'ID Percobaan',
                'Tanggal Pengerjaan',
                'Nama User',
                'Username',
                'Email',
                'Section / Materi',
                'Spesialisasi / Skema',
                'Jumlah Soal',
                'Jawaban Benar',
                'Skor (%)',
                'Minimal Skor (%)',
                'Status Kelulusan',
            ]);

            $counter = 0;

            $query->chunk(250, function ($attempts) use ($handle, &$counter) {
                foreach ($attempts as $attempt) {
                    $counter++;
                    $user = $attempt->user;
                    $sec = $attempt->section;
                    $schemas = $sec ? $sec->learningSchemas->pluck('title')->join('; ') : '-';
                    $scorePct = $attempt->getScorePercentage();
                    $passingScore = $attempt->passing_score ?? ($sec?->passing_score ?? 70);
                    $status = $attempt->isPassed() ? 'Lulus' : 'Belum Lulus';

                    fputcsv($handle, [
                        $counter,
                        $attempt->id,
                        $attempt->attempted_at ? $attempt->attempted_at->format('Y-m-d H:i:s') : '-',
                        $user?->name ?? 'User dihapus',
                        $user?->username ?? '-',
                        $user?->email ?? '-',
                        $sec?->title ?? '-',
                        $schemas ?: '-',
                        $attempt->total_questions,
                        $attempt->correct_answers,
                        $scorePct.'%',
                        $passingScore.'%',
                        $status,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Apply optional search, status, and section filters to query.
     *
     * @param  Builder<QuizAttempt>  $query
     * @param  array<string, mixed>  $filters
     */
    public function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['section_id'])) {
            $query->where('section_id', (int) $filters['section_id']);
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', $search)
                    ->orWhere('username', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'passed') {
                $query->where(function ($q) {
                    $q->where('is_passed', true)
                        ->orWhereRaw('total_questions > 0 AND ROUND(correct_answers * 100.0 / total_questions) >= COALESCE(passing_score, 70)');
                });
            } elseif ($filters['status'] === 'failed') {
                $query->where(function ($q) {
                    $q->where('is_passed', false)
                        ->where(function ($sq) {
                            $sq->whereNull('passing_score')
                                ->orWhereRaw('total_questions = 0 OR ROUND(correct_answers * 100.0 / total_questions) < passing_score');
                        });
                });
            }
        }
    }
}
