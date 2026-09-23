<?php

use App\Models\LearningSchema;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

test('admin can download csv template', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.users.template'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('name,username,email,password,role,is_active');
});

test('non admin user cannot access csv template or import csv', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get(route('admin.users.template'))->assertRedirect(route('user.dashboard'));
    $this->actingAs($user)->post(route('admin.users.import'))->assertRedirect(route('user.dashboard'));
});

test('guest cannot access csv template or import csv', function () {
    $this->get(route('admin.users.template'))->assertRedirect(route('login'));
    $this->post(route('admin.users.import'))->assertRedirect(route('login'));
});

test('admin can import multi user via csv', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $csvContent = implode("\n", [
        'name,username,email,password,role,is_active',
        'User Pertama,user1,user1@example.com,Password123!,user,1',
        'User Kedua,user2,user2@example.com,Password123!,user,1',
    ]);

    $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

    $response = $this->actingAs($admin)->post(route('admin.users.import'), [
        'csv_file' => $file,
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'Berhasil mengimpor 2 user.');

    $this->assertDatabaseHas('users', [
        'name' => 'User Pertama',
        'username' => 'user1',
        'email' => 'user1@example.com',
        'role' => 'user',
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('users', [
        'name' => 'User Kedua',
        'username' => 'user2',
        'email' => 'user2@example.com',
        'role' => 'user',
        'is_active' => true,
    ]);
});

test('admin can import users via csv and auto enroll learning schemas', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $schema = LearningSchema::create([
        'title' => 'Materi Pembelajaran 1',
        'description' => 'Deskripsi materi',
        'is_active' => true,
    ]);

    $csvContent = implode("\n", [
        'name,username,email,password,role,is_active',
        'User Enrolled,userenrolled,userenrolled@example.com,Password123!,user,1',
    ]);

    $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

    $response = $this->actingAs($admin)->post(route('admin.users.import'), [
        'csv_file' => $file,
        'schema_ids' => [$schema->id],
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $newUser = User::where('username', 'userenrolled')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->learningSchemas->pluck('id'))->toContain($schema->id);
});

test('import csv handles invalid rows and displays warnings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['username' => 'existinguser', 'email' => 'existing@example.com']);

    $csvContent = implode("\n", [
        'name,username,email,password,role,is_active',
        'Valid User,validuser,valid@example.com,Password123!,user,1',
        'Duplicate Username,existinguser,different@example.com,Password123!,user,1',
        'Short Password,shortpass,short@example.com,pass,user,1',
    ]);

    $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

    $response = $this->actingAs($admin)->post(route('admin.users.import'), [
        'csv_file' => $file,
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'Berhasil mengimpor 1 user.');
    $response->assertSessionHas('import_errors');

    $errors = session('import_errors');
    expect($errors)->toBeArray()->toHaveCount(2);

    $this->assertDatabaseHas('users', ['username' => 'validuser']);
});
