<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserCsvImportService
{
    /**
     * Import users from an uploaded CSV file.
     *
     * @param  UploadedFile  $file
     * @param  array  $schemaIds  Learning schema IDs to enroll imported users into
     * @return array{success_count: int, errors: array<string>}
     */
    public function import(UploadedFile $file, array $schemaIds = []): array
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

        $requiredHeaders = ['name', 'username', 'email', 'password'];
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

        $seenUsernames = [];
        $seenEmails = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineNumber++;

            // Skip empty rows
            if (empty(array_filter($row, fn ($val) => trim((string) $val) !== ''))) {
                continue;
            }

            $name = isset($headerMap['name']) && isset($row[$headerMap['name']]) ? trim($row[$headerMap['name']]) : '';
            $username = isset($headerMap['username']) && isset($row[$headerMap['username']]) ? trim($row[$headerMap['username']]) : '';
            $email = isset($headerMap['email']) && isset($row[$headerMap['email']]) ? trim($row[$headerMap['email']]) : '';
            $password = isset($headerMap['password']) && isset($row[$headerMap['password']]) ? trim($row[$headerMap['password']]) : '';
            $roleRaw = isset($headerMap['role']) && isset($row[$headerMap['role']]) ? trim(strtolower($row[$headerMap['role']])) : 'user';
            $isActiveRaw = isset($headerMap['is_active']) && isset($row[$headerMap['is_active']]) ? trim(strtolower($row[$headerMap['is_active']])) : '1';

            $role = in_array($roleRaw, ['admin', 'user'], true) ? $roleRaw : 'user';
            $isActive = ! in_array($isActiveRaw, ['0', 'false', 'off', 'no'], true);

            // Check duplicate in current CSV file batch
            $lowerUsername = strtolower($username);
            $lowerEmail = strtolower($email);

            if ($username !== '' && isset($seenUsernames[$lowerUsername])) {
                $errors[] = "Baris #{$lineNumber}: Username '{$username}' duplikat dalam file CSV yang sama.";

                continue;
            }

            if ($email !== '' && isset($seenEmails[$lowerEmail])) {
                $errors[] = "Baris #{$lineNumber}: Email '{$email}' duplikat dalam file CSV yang sama.";

                continue;
            }

            $validator = Validator::make([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
            ], [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:50|alpha_dash|unique:users,username',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => ['required', Password::min(8)],
                'role' => 'required|in:admin,user',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'username.required' => 'Username wajib diisi.',
                'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan garis bawah.',
                'username.unique' => 'Username sudah digunakan di sistem.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar di sistem.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
            ]);

            if ($validator->fails()) {
                $rowErrors = implode(' ', $validator->errors()->all());
                $identifier = $username ?: ($email ?: "Baris #{$lineNumber}");
                $errors[] = "Baris #{$lineNumber} ({$identifier}): {$rowErrors}";

                continue;
            }

            // Create User
            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'is_active' => $isActive,
            ]);

            // Sync Learning Schemas if selected
            if (! empty($schemaIds)) {
                $pivot = [];
                foreach ($schemaIds as $id) {
                    $pivot[(int) $id] = ['enrolled_at' => now(), 'status' => 'active'];
                }
                $user->learningSchemas()->sync($pivot);
            }

            $seenUsernames[$lowerUsername] = true;
            $seenEmails[$lowerEmail] = true;
            $successCount++;
        }

        fclose($handle);

        return [
            'success_count' => $successCount,
            'errors' => $errors,
        ];
    }

    /**
     * Generate sample CSV template content.
     *
     * @return string
     */
    public function generateTemplateContent(): string
    {
        $headers = ['name', 'username', 'email', 'password', 'role', 'is_active'];

        $rows = [
            $headers,
            ['Budi Santoso', 'budisantoso', 'budi@example.com', 'Password123!', 'user', '1'],
            ['Siti Rahma', 'sitirahma', 'siti@example.com', 'Password123!', 'user', '1'],
            ['Admin Master', 'adminmaster', 'adminmaster@example.com', 'Password123!', 'admin', '1'],
        ];

        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content ?: '';
    }
}
