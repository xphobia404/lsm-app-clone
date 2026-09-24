<?php

use App\Models\LearningSchema;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can view schema show page without undefined adminPreview variable error', function () {
    $user = User::factory()->create(['role' => 'user']);
    $schema = LearningSchema::create(['title' => 'Materi Matematika', 'is_active' => true]);

    $user->learningSchemas()->attach($schema->id, ['enrolled_at' => now(), 'status' => 'active']);

    $response = $this->actingAs($user)->get(route('user.schemas.show', $schema));

    $response->assertStatus(200);
    $response->assertSee('Materi Matematika');
});

test('admin can preview schema show page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $schema = LearningSchema::create(['title' => 'Materi Fisika', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(route('admin.learning-schemas.preview.show', $schema));

    $response->assertStatus(200);
    $response->assertSee('Materi Fisika');
    $response->assertSee('Mode preview admin');
});
