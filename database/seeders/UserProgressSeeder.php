<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Database\Seeder;

class UserProgressSeeder extends Seeder
{
    public function run(): void
    {
        $users    = User::where('role', 'user')->where('is_active', true)->get();
        $sections = Section::where('is_active', true)->get();

        foreach ($users as $user) {
            foreach ($sections as $index => $section) {
                // Setiap user punya progress berbeda-beda
                $scenario = ($user->id + $index) % 3;

                $status     = match ($scenario) {
                    0 => 'completed',
                    1 => 'in_progress',
                    default => 'not_started',
                };

                UserProgress::create([
                    'user_id'      => $user->id,
                    'section_id'   => $section->id,
                    'status'       => $status,
                    'started_at'   => in_array($status, ['in_progress', 'completed'])
                        ? now()->subDays(rand(1, 30))
                        : null,
                    'completed_at' => $status === 'completed'
                        ? now()->subDays(rand(0, 10))
                        : null,
                ]);
            }
        }
    }
}
