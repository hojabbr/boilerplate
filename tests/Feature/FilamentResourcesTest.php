<?php

use App\Domains\Contact\Models\ContactSubmission;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\BlogPosts\RelationManagers\TagsRelationManager;
use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;

test('unauthenticated user is redirected from admin panel', function (): void {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('Filament resource list URLs are registered', function (): void {
    expect(BlogPostResource::getUrl('index'))->toContain('blog-posts');
    expect(ContactSubmissionResource::getUrl('index'))->toContain('contact-submissions');
});

test('ContactSubmissionResource has view page route', function (): void {
    $submission = ContactSubmission::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'subject' => 'Subj',
        'message' => 'Msg',
    ]);

    $url = ContactSubmissionResource::getUrl('view', ['record' => $submission]);
    expect($url)->toContain('contact-submissions')->toContain((string) $submission->getKey());
});

test('BlogPostResource has tags relation manager', function (): void {
    $relations = BlogPostResource::getRelations();
    expect($relations)->toContain(TagsRelationManager::class);
});
