<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guest cannot upload campaign images', function () {
    $response = $this->postJson(route('admin.campaign.upload-image'), [
        'file' => UploadedFile::fake()->image('test.png'),
    ]);

    $response->assertStatus(401);
});

test('authenticated user can upload image for campaign editor', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('admin.campaign.upload-image'), [
        'file' => UploadedFile::fake()->image('banner.jpg', 600, 400),
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['location']);

    $location = $response->json('location');
    expect($location)->toContain('/storage/campaigns/images/');
});

test('non-image files are rejected', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('admin.campaign.upload-image'), [
        'file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ]);

    $response->assertStatus(422);
});

