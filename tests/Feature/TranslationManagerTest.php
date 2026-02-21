<?php

use App\Core\Services\TranslationKeyScanner;
use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\TranslationLoader\LanguageLine;

beforeEach(function (): void {
    $guard = 'web';
    Permission::firstOrCreate(['name' => 'manage translations', 'guard_name' => $guard]);
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
    $adminRole->givePermissionTo(Permission::where('guard_name', $guard)->pluck('name'));
});

test('user with manage translations permission can use translation manager gate', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('manage translations');
    $this->actingAs($user);

    expect(\Illuminate\Support\Facades\Gate::allows('use-translation-manager'))->toBeTrue();
    expect($user->can('manage translations'))->toBeTrue();
});

test('user without manage translations permission cannot use translation manager gate', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);
    expect(\Illuminate\Support\Facades\Gate::allows('use-translation-manager'))->toBeFalse();
});

test('user with manage translations permission can view any language lines', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('manage translations');
    $this->actingAs($user);

    expect($user->can('viewAny', LanguageLine::class))->toBeTrue();
});

test('user without manage translations permission cannot view any language lines', function (): void {
    $user = User::factory()->create();

    expect($user->can('viewAny', LanguageLine::class))->toBeFalse();
});

test('admin panel translation manager list returns 403 for user without manage translations', function (): void {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $user->revokePermissionTo('manage translations');
    $this->actingAs($user);

    $response = $this->get('/admin/language-lines');

    $response->assertForbidden();
});

test('translations scan command adds missing keys from scanned paths', function (): void {
    $fixtureDir = storage_path('app/test-scan-'.uniqid());
    File::makeDirectory($fixtureDir, 0755, true);
    File::put($fixtureDir.'/fixture.php', "<?php\n__('test.scan.key');\ntrans('test.scan.other');\n");

    try {
        $scanner = app(TranslationKeyScanner::class);
        $result = $scanner->scan(true, [$fixtureDir]);

        expect($result['found'])->toBe(2)
            ->and($result['added'])->toBe(2)
            ->and(LanguageLine::query()->where('group', '*')->where('key', 'test.scan.key')->exists())->toBeTrue()
            ->and(LanguageLine::query()->where('group', '*')->where('key', 'test.scan.other')->exists())->toBeTrue();
    } finally {
        File::deleteDirectory($fixtureDir);
    }
});

test('translations scan command dry run does not persist', function (): void {
    $fixtureDir = storage_path('app/test-scan-dry-'.uniqid());
    File::makeDirectory($fixtureDir, 0755, true);
    File::put($fixtureDir.'/fixture.php', "<?php\n__('test.dry.run.key');\n");

    try {
        $scanner = app(TranslationKeyScanner::class);
        $result = $scanner->scan(false, [$fixtureDir]);

        expect($result['to_add_keys'])->toContain('test.dry.run.key')
            ->and(LanguageLine::query()->where('group', '*')->where('key', 'test.dry.run.key')->exists())->toBeFalse();
    } finally {
        File::deleteDirectory($fixtureDir);
    }
});

test('translations csv export returns 403 without permission', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('translations.export-csv'));

    $response->assertForbidden();
});

test('translations csv export returns csv when user has permission', function (): void {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    $user = User::factory()->create();
    $user->givePermissionTo('manage translations');
    $this->actingAs($user);

    $response = $this->get(route('translations.export-csv'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});
