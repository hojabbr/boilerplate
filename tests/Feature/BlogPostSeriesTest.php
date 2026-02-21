<?php

use App\Domains\Auth\Models\User;
use App\Domains\Blog\Models\BlogPostSeries;
use Illuminate\Support\Facades\Artisan;

beforeEach(function (): void {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
});

test('BlogPostSeries stores and retrieves array casts correctly', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('manage blog');

    $series = BlogPostSeries::create([
        'user_id' => $user->id,
        'name' => 'Test series',
        'purpose' => 'Test purpose',
        'objective' => 'Test objective',
        'topics' => 'test topics',
        'start_date' => now(),
        'end_date' => now()->addWeek(),
        'days_of_week' => [0, 2, 4],
        'run_at_hours' => [9, 14, 18],
        'posts_per_run' => 1,
        'total_posts_limit' => 5,
        'provider' => 'openai',
        'length' => 'short',
        'language_ids' => [1, 2],
        'generate_image' => false,
        'generate_audio' => false,
        'publish_immediately' => false,
    ]);

    $series->refresh();

    expect($series->days_of_week)->toBe([0, 2, 4])
        ->and($series->run_at_hours)->toBe([9, 14, 18])
        ->and($series->language_ids)->toBe([1, 2]);
});

test('blog:run-scheduled-series command runs successfully when no series are due', function (): void {
    $exitCode = Artisan::call('blog:run-scheduled-series');

    expect($exitCode)->toBe(0);
});

test('BlogPostSeriesPolicy allows viewAny only when user can manage blog', function (): void {
    $userWithBlog = User::factory()->create();
    $userWithBlog->givePermissionTo('manage blog');

    $userWithoutBlog = User::factory()->create();

    $policy = new \App\Domains\Blog\Policies\BlogPostSeriesPolicy;

    expect($policy->viewAny($userWithBlog))->toBeTrue()
        ->and($policy->viewAny($userWithoutBlog))->toBeFalse();
});
