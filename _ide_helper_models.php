<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property \Carbon\CarbonImmutable|null $published_at
 * @extends Model<BlogPost>
 * @property int $id
 * @property int $language_id
 * @property string $slug
 * @property string|null $title
 * @property string|null $excerpt
 * @property string|null $body
 * @property string|null $meta_description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Language $language
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost byLocale(string $code)
 * @method static \Database\Factories\BlogPostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBlogPost {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $subject
 * @property string $message
 * @property string|null $locale
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContactSubmission {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string $label
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFeatureFlag {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property int $sort_order
 * @property array<array-key, mixed>|null $title
 * @property array<array-key, mixed>|null $subtitle
 * @property array<array-key, mixed>|null $body
 * @property array<array-key, mixed>|null $cta_text
 * @property array<array-key, mixed>|null $cta_url
 * @property bool $is_active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LandingSectionItem> $items
 * @property-read int|null $items_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereCtaText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereCtaUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandingSection {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $landing_section_id
 * @property int $sort_order
 * @property array<array-key, mixed>|null $title
 * @property array<array-key, mixed>|null $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\LandingSection $landingSection
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereLandingSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandingSectionItem {}
}

namespace App\Models{
/**
 * @extends Model<Language>
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $script
 * @property string|null $regional
 * @property bool $is_default
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BlogPost> $blogPosts
 * @property-read int|null $blog_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $pages
 * @property-read int|null $pages_count
 * @method static \Database\Factories\LanguageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereRegional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereScript($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLanguage {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $slug
 * @property array<array-key, mixed>|null $title
 * @property array<array-key, mixed>|null $body
 * @property string $type
 * @property bool $is_active
 * @property bool $show_in_navigation
 * @property bool $show_in_footer
 * @property int $order
 * @property array<array-key, mixed>|null $meta_title
 * @property array<array-key, mixed>|null $meta_description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereShowInFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereShowInNavigation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPage {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property array<array-key, mixed>|null $company_name
 * @property array<array-key, mixed>|null $tagline
 * @property array<array-key, mixed>|null $address
 * @property string|null $email
 * @property string|null $phone
 * @property array<array-key, mixed>|null $social_links
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSocialLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSetting {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Carbon\CarbonImmutable|null $two_factor_confirmed_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

