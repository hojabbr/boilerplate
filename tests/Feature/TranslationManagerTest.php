<?php

use App\Domains\Auth\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
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

    expect(Gate::allows('use-translation-manager'))->toBeTrue();
    expect($user->can('manage translations'))->toBeTrue();
});

test('user without manage translations permission cannot use translation manager gate', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);
    expect(Gate::allows('use-translation-manager'))->toBeFalse();
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
    $this->seed(RoleAndPermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    Role::findByName('admin')->revokePermissionTo('manage translations');
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    $this->actingAs($user);

    $response = $this->get('/admin/language-lines');

    $response->assertForbidden();
});

test('translations csv export returns 403 without permission', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('translations.export-csv'));

    $response->assertForbidden();
});

test('translations csv export returns csv when user has permission', function (): void {
    $this->seed(RoleAndPermissionSeeder::class);
    $user = User::factory()->create();
    $user->givePermissionTo('manage translations');
    $this->actingAs($user);

    $response = $this->get(route('translations.export-csv'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});
