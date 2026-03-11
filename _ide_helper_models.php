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


namespace App\Core\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureFlag query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFeatureFlag {}
}

namespace App\Core\Models{
/**
 * @extends Model<Language>
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Blog\Models\BlogPost> $blogPosts
 * @property-read int|null $blog_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Faq\Models\Faq> $faqs
 * @property-read int|null $faqs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Page\Models\Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Testimonial\Models\Testimonial> $testimonials
 * @property-read int|null $testimonials_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language enabled()
 * @method static \Database\Factories\LanguageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLanguage {}
}

namespace App\Core\Models{
/**
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereLocales(string $column, array $locales)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSetting {}
}

namespace App\Domains\Auth\Models{
/**
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Domains\Blog\Models{
/**
 * @property \Carbon\CarbonImmutable|null $published_at
 * @extends Model<BlogPost>
 * @property-read \App\Core\Models\Language|null $language
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Domains\Blog\Models\BlogPostSeries|null $series
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Blog\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost byLocale(string $code)
 * @method static \Database\Factories\BlogPostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPost withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBlogPost {}
}

namespace App\Domains\Blog\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $purpose
 * @property string|null $objective
 * @property string|null $topics
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property array<int, int> $days_of_week
 * @property array<int, int> $run_at_hours
 * @property int $posts_per_run
 * @property int|null $total_posts_limit
 * @property string $provider
 * @property string $length
 * @property array<int, int> $language_ids
 * @property bool $generate_image
 * @property string $image_style
 * @property array<int, string>|null $image_styles
 * @property bool $publish_immediately
 * @property \Carbon\Carbon|null $last_run_at
 * @property int $posts_generated
 * @property bool $is_active
 * @extends Model<BlogPostSeries>
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Blog\Models\BlogPost> $blogPosts
 * @property-read int|null $blog_posts_count
 * @property-read \App\Domains\Auth\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPostSeries active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPostSeries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPostSeries newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlogPostSeries query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBlogPostSeries {}
}

namespace App\Domains\Blog\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @extends Model<Tag>
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Blog\Models\BlogPost> $blogPosts
 * @property-read int|null $blog_posts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
}

namespace App\Domains\Contact\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSubmission withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContactSubmission {}
}

namespace App\Domains\Faq\Models{
/**
 * @extends Model<Faq>
 * @property-read \App\Core\Models\Language|null $language
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq byLocale(string $code)
 * @method static \Database\Factories\FaqFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFaq {}
}

namespace App\Domains\Landing\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Landing\Models\LandingSectionItem> $items
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSection withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandingSection {}
}

namespace App\Domains\Landing\Models{
/**
 * @property-read \App\Domains\Landing\Models\LandingSection|null $landingSection
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingSectionItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandingSectionItem {}
}

namespace App\Domains\Page\Models{
/**
 * @extends Model<Page>
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page active()
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPage {}
}

namespace App\Domains\Testimonial\Models{
/**
 * @extends Model<Testimonial>
 * @property-read \App\Core\Models\Language|null $language
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial byLocale(string $code)
 * @method static \Database\Factories\TestimonialFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTestimonial {}
}

