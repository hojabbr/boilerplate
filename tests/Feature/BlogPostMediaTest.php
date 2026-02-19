<?php

use App\Models\BlogPost;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Pennant\Feature;

beforeEach(function () {
    Feature::activate('blog');
});

test('blog show returns post with gallery videos and documents', function () {
    refreshApplicationWithLocale('en');

    $post = BlogPost::factory()->create([
        'slug' => 'test-post-media',
        'published_at' => now(),
    ]);

    $response = $this->get(route('blog.show', ['slug' => $post->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('blog/Show')
        ->has('post')
        ->has('post.gallery')
        ->has('post.videos')
        ->has('post.documents')
        ->where('post.title', $post->title)
    );

    $postData = $response->inertiaProps('post');
    expect($postData['gallery'])->toBeArray();
    expect($postData['videos'])->toBeArray();
    expect($postData['documents'])->toBeArray();
});

test('force deleting blog post removes associated media', function () {
    Storage::fake('public');

    $post = BlogPost::factory()->create([
        'slug' => 'cascade-delete-test',
        'published_at' => now(),
    ]);

    $file = UploadedFile::fake()->image('test.jpg');
    $post->addMedia($file)->toMediaCollection('gallery');

    expect($post->getMedia('gallery'))->toHaveCount(1);

    $mediaId = $post->getFirstMedia('gallery')->id;

    $post->forceDelete();

    expect(\Spatie\MediaLibrary\MediaCollections\Models\Media::find($mediaId))->toBeNull();
});
