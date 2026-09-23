<?php

use App\Models\Content;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('content detail viewer renders responsive table container and styling', function () {
    $user = User::factory()->create(['role' => 'user']);
    $section = Section::create(['title' => 'Sekolah', 'is_active' => true]);

    $tableHtml = '<table><thead><tr><th>Kolom 1</th><th>Kolom 2</th></tr></thead><tbody><tr><td>Isi 1</td><td>Isi 2</td></tr></tbody></table>';

    $content = Content::create([
        'section_id' => $section->id,
        'title' => 'Materi Tabel Responsif',
        'content_type' => 'text',
        'body' => json_encode($tableHtml),
        'content_order' => 1,
        'is_active' => true,
    ]);

    $view = $this->blade('<x-content-detail-viewer :content="$content" :section="$section" mode="user" />', [
        'content' => $content,
        'section' => $section,
    ]);

    $html = (string) $view;

    expect($html)->toContain('.table-responsive');
    expect($html)->toContain('viewer.querySelectorAll(\'table\')');
});
