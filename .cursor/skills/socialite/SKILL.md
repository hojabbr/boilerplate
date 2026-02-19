---
name: laravel-socialite
description: "Enables OAuth social authentication via third-party providers (Google, GitHub, Facebook, LinkedIn, X, Slack, GitLab, Bitbucket). Activates when adding social login, configuring OAuth routes, handling provider callbacks, storing user tokens, invoking scopes, or when the user mentions social, OAuth, login with Google, Facebook, LinkedIn, Slack, GitHub, or SSO."
license: MIT
metadata:
  author: laravel
---

# Laravel Socialite (OAuth Social Login)

## When to Apply

Activate this skill when:

- Installing and configuring Laravel Socialite
- Defining social login redirects and callbacks
- Authenticating users via OAuth providers
- Saving provider user information (tokens, IDs, emails)
- Adding scopes or optional parameters to OAuth requests
- Handling stateless API social authentication
- Testing social authentication flows

## Documentation

Use `search-docs` to open the Socialite section of the **Laravel 12.x** documentation.  [oai_citation:1‡Laravel](https://laravel.com/docs/12.x/socialite?utm_source=chatgpt.com)

## Introduction

Laravel Socialite provides a simple, expressive way to authenticate users with OAuth providers such as:

- Facebook  
- X (formerly Twitter)  
- LinkedIn  
- Google  
- GitHub  
- GitLab  
- Bitbucket  
- Slack  [oai_citation:2‡Laravel](https://laravel.com/docs/12.x/socialite?utm_source=chatgpt.com)

Adapters for additional services are available via the **Socialite Providers** community package.

---

## Installation

Add Socialite to your Laravel project:

```bash
composer require laravel/socialite

This installs the package required to support social OAuth flows.  ￼

⸻

Configuration

Before using Socialite, register your OAuth credentials from the provider’s developer dashboard and place them in config/services.php:

'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => env('GITHUB_REDIRECT_URL'),
],

If the redirect uses a relative path, Laravel resolves it to a full URL automatically.  ￼

⸻

Authentication Routing

Define two routes:
	•	One to redirect users to the provider
	•	One to handle the callback from the provider

Example:

use Laravel\Socialite\Facades\Socialite;

Route::get('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();

    // Access user properties and token
});

Socialite’s redirect() handles sending users to the provider and user() captures the authenticated user after approval by the provider.  ￼

⸻

Authentication & Storage

Once the provider returns the user, you can:
	•	Determine if the user exists in your database
	•	Create or update the user
	•	Log the user in

Example:

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::get('/auth/callback', function () {
    $githubUser = Socialite::driver('github')->user();

    $user = User::updateOrCreate([
        'github_id' => $githubUser->id,
    ], [
        'name' => $githubUser->name,
        'email' => $githubUser->email,
        'github_token' => $githubUser->token,
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});

This pattern stores provider IDs and tokens to manage user authentication.  ￼

⸻

Access Scopes

Before redirecting users, you can define scopes to request additional permissions:

return Socialite::driver('github')
        ->scopes(['read:user', 'public_repo'])
        ->redirect();

Use setScopes() to override previously set scopes entirely.  ￼

⸻

Slack Bot Scopes

For Slack, Socialite supports:
	•	Bot tokens (prefixed with xoxb-)
	•	User tokens (prefixed with xoxp-)

To generate a bot token:

return Socialite::driver('slack')
        ->asBotUser()
        ->setScopes([...])
        ->redirect();

And in callback:

$user = Socialite::driver('slack')->asBotUser()->user();

Only the token property is available when retrieving a bot token.  ￼

⸻

Optional Parameters

Include extra parameters in the OAuth request:

return Socialite::driver('google')
        ->with(['hd' => 'example.com'])
        ->redirect();

Do not pass reserved parameters like state or response_type in with().  ￼

⸻

Retrieving User Details

After callback, Socialite returns a user object with properties such as:
	•	token
	•	refreshToken
	•	expiresIn
	•	getId(), getName(), getEmail(), getAvatar()

Use these to populate local user models.  ￼

Stateless API Authentication

Disable session state if your app doesn’t use cookies:

return Socialite::driver('google')->stateless()->user();
```  [oai_citation:11‡Laravel](https://laravel.com/docs/12.x/socialite?utm_source=chatgpt.com)

---

## Testing Socialite

Laravel Socialite offers tools to **fake OAuth providers** for testing flows without actual external requests.  [oai_citation:12‡Laravel](https://laravel.com/docs/12.x/socialite?utm_source=chatgpt.com)

---

## Common Pitfalls

- Redirect URLs must exactly match provider config
- Forgetting to add credentials to `config/services.php`
- Not handling stateless API authentication when needed

---

## Summary

| Step | Description |
|------|-------------|
| Install | `composer require laravel/socialite` |
| Configure | Add provider credentials in `config/services.php` |
| Routes | Create redirect & callback endpoints |
| Authenticate | Retrieve user with `Socialite::driver(...)->user()` |
| Storage | Save and login the social user |
| Scopes | Add OAuth scopes for extra permissions |
| Optional Params | Use `with()` to include extra OAuth parameters |
| Statelsss | Use `stateless()` for API-only apps |