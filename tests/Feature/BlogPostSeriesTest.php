<?php

use App\Domains\Auth\Models\User;
use App\Domains\Blog\Models\BlogPostSeries;
use Carbon\Carbon;
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

test('blog:run-scheduled-series claims run before dispatching job', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('manage blog');

    $now = Carbon::create(2026, 2, 23, 14, 30, 0); // Monday 14:30
    Carbon::setTestNow($now);

    $series = BlogPostSeries::create([
        'user_id' => $user->id,
        'name' => 'Claim test',
        'purpose' => 'Purpose',
        'objective' => 'Objective',
        'topics' => 'Topics',
        'start_date' => $now,
        'end_date' => $now->copy()->addWeek(),
        'days_of_week' => [(int) $now->format('w')],
        'run_at_hours' => [(int) $now->format('G')],
        'posts_per_run' => 1,
        'total_posts_limit' => 2,
        'provider' => 'openai',
        'length' => 'short',
        'language_ids' => [1],
        'generate_image' => false,
        'publish_immediately' => false,
        'is_active' => true,
    ]);

    $series->refresh();
    $initialCount = (int) $series->posts_generated;

    Artisan::call('blog:run-scheduled-series');

    Carbon::setTestNow();

    $series->refresh();
    expect($series->last_run_at)->not->toBeNull()
        ->and((int) $series->posts_generated)->toBe($initialCount + 1);
});

test('BlogPostSeriesPolicy allows viewAny only when user can manage blog', function (): void {
    $userWithBlog = User::factory()->create();
    $userWithBlog->givePermissionTo('manage blog');

    $userWithoutBlog = User::factory()->create();

    $policy = new \App\Domains\Blog\Policies\BlogPostSeriesPolicy;

    expect($policy->viewAny($userWithBlog))->toBeTrue()
        ->and($policy->viewAny($userWithoutBlog))->toBeFalse();
});
