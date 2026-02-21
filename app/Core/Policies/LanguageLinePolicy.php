<?php

namespace App\Core\Policies;

use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLinePolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::allows('use-translation-manager');
    }

    public function view(User $user, LanguageLine $languageLine): bool
    {
        return Gate::allows('use-translation-manager');
    }

    public function create(User $user): bool
    {
        return Gate::allows('use-translation-manager');
    }

    public function update(User $user, LanguageLine $languageLine): bool
    {
        return Gate::allows('use-translation-manager');
    }

    public function delete(User $user, LanguageLine $languageLine): bool
    {
        return Gate::allows('use-translation-manager');
    }

    public function restore(User $user, LanguageLine $languageLine): bool
    {
        return Gate::allows('use-translation-manager');
    }

    public function forceDelete(User $user, LanguageLine $languageLine): bool
    {
        return Gate::allows('use-translation-manager');
    }
}
