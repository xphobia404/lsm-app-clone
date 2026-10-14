<?php

namespace App\Services;

use App\Models\Section;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class QuizCsvImportService
{
    /**
     * Import quizzes from an uploaded CSV file into a specific section.
     *
     * @return array{success_count: int, errors: array<string>}
     */
    public function import(UploadedFile $file, Section $section): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return [
                'success_count' => 0,
                'errors' => ['Gagal membuka file CSV.'],
            ];
        }

        // Detect BOM (Byte Order Mark) if present (UTF-8 BOM)
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read header line
        $firstLine = fgets($handle);
        if (! $firstLine) {
            fclose($handle);

            return [
                'success_count' => 0,
                'errors' => ['File CSV kosong.'],
            ];
        }

        // Detect delimiter (comma or semicolon)
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        $headers = str_getcsv($firstLine, $delimiter);
        $headers = array_map(fn ($h) => trim(strtolower((string) $h)), $headers);

        $headerMap = array_flip($headers);

        $requiredHeaders = ['question', 'option_a', 'option_b', 'correct_answer'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if (! empty($missingHeaders)) {
            fclose($handle);

            return [
                'success_count' => 0,
                'errors' => ['Header CSV tidak valid. Kolom yang wajib ada: '.implode(', ', $requiredHeaders)],
            ];
        }

        $successCount = 0;
        $errors = [];
        $lineNumber = 1;

        $currentMaxOrder = (int) $section->quizzes()->max('quiz_order');

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineNumber++;

            // Skip empty rows
            if (empty(array_filter($row, fn ($val) => trim((string) $val) !== ''))) {
                continue;
            }

            $question = isset($headerMap['question']) && isset($row[$headerMap['question']])
                ? trim((string) $row[$headerMap['question']])
                : '';
            $optionA = isset($headerMap['option_a']) && isset($row[$headerMap['option_a']])
                ? trim((string) $row[$headerMap['option_a']])
                : '';
            $optionB = isset($headerMap['option_b']) && isset($row[$headerMap['option_b']])
                ? trim((string) $row[$headerMap['option_b']])
                : '';
            $optionC = isset($headerMap['option_c']) && isset($row[$headerMap['option_c']])
                ? trim((string) $row[$headerMap['option_c']])
                : '';
            $optionD = isset($headerMap['option_d']) && isset($row[$headerMap['option_d']])
                ? trim((string) $row[$headerMap['option_d']])
                : '';
            $correctAnswer = isset($headerMap['correct_answer']) && isset($row[$headerMap['correct_answer']])
                ? strtolower(trim((string) $row[$headerMap['correct_answer']]))
                : '';
            $explanation = isset($headerMap['explanation']) && isset($row[$headerMap['explanation']])
                ? trim((string) $row[$headerMap['explanation']])
                : '';

            $validator = Validator::make([
                'question' => $question,
                'option_a' => $optionA,
                'option_b' => $optionB,
                'option_c' => $optionC,
                'option_d' => $optionD,
                'correct_answer' => $correctAnswer,
                'explanation' => $explanation,
            ], [
                'question' => 'required|string|max:1000',
                'option_a' => 'required|string|max:255',
                'option_b' => 'required|string|max:255',
                'option_c' => 'nullable|string|max:255',
                'option_d' => 'nullable|string|max:255',
                'correct_answer' => 'required|in:a,b,c,d',
                'explanation' => 'nullable|string|max:2000',
            ], [
                'question.required' => 'Pertanyaan wajib diisi.',
                'question.max' => 'Pertanyaan maksimal 1.000 karakter.',
                'option_a.required' => 'Opsi A wajib diisi.',
                'option_b.required' => 'Opsi B wajib diisi.',
                'correct_answer.required' => 'Kunci jawaban benar wajib diisi (A, B, C, atau D).',
                'correct_answer.in' => 'Kunci jawaban benar harus antara A, B, C, atau D.',
            ]);

            if ($validator->fails()) {
                $rowErrors = implode(' ', $validator->errors()->all());
                $snippet = mb_strimwidth($question, 0, 30, '...');
                $identifier = $snippet ? "\"{$snippet}\"" : "Baris #{$lineNumber}";
                $errors[] = "Baris #{$lineNumber} ({$identifier}): {$rowErrors}";

                continue;
            }

            // Check if correct_answer points to empty optional option
            if ($correctAnswer === 'c' && $optionC === '') {
                $errors[] = "Baris #{$lineNumber}: Kunci jawaban 'C' dipilih namun Opsi C kosong.";

                continue;
            }

            if ($correctAnswer === 'd' && $optionD === '') {
                $errors[] = "Baris #{$lineNumber}: Kunci jawaban 'D' dipilih namun Opsi D kosong.";

                continue;
            }

            $currentMaxOrder++;

            $section->quizzes()->create([
                'question' => $question,
                'option_a' => $optionA,
                'option_b' => $optionB,
                'option_c' => $optionC !== '' ? $optionC : null,
                'option_d' => $optionD !== '' ? $optionD : null,
                'correct_answer' => $correctAnswer,
                'explanation' => $explanation !== '' ? $explanation : null,
                'quiz_order' => $currentMaxOrder,
                'is_active' => true,
            ]);

            $successCount++;
        }

        fclose($handle);

        return [
            'success_count' => $successCount,
            'errors' => $errors,
        ];
    }

    /**
     * Generate sample CSV template content with UTF-8 BOM.
     */
    public function generateTemplateContent(): string
    {
        $headers = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'explanation'];

        $rows = [
            $headers,
            [
                'Apa kepanjangan dari LSM?',
                'Lembaga Swadaya Masyarakat',
                'Lembaga Sosial Mandiri',
                'Layanan Sosial Masyarakat',
                'Lembaga Sahabat Mandiri',
                'A',
                'LSM merupakan singkatan dari Lembaga Swadaya Masyarakat.',
            ],
            [
                'Manakah yang menjadi fokus utama kegiatan organisasi LSM?',
                'Pemberdayaan dan advokasi masyarakat',
                'Mencari profit komersial maksimal',
                'Menjual produk manufaktur',
                'Memungut pajak warga',
                'A',
                'LSM berfokus pada kegiatan nirlaba, advokasi, dan pemberdayaan masyarakat.',
            ],
            [
                'Apakah LSM berorientasi pada laba komersial (profit)?',
                'Tidak (Organisasi Nirlaba / Non-profit)',
                'Ya (Organisasi Profit)',
                '',
                '',
                'A',
                'LSM adalah badan hukum organisasi nirlaba yang tidak membagikan keuntungan kepada pendiri/anggota.',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        // Add UTF-8 BOM for Microsoft Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content ?: '';
    }
}
