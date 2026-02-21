<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Pennant\Feature;

/**
 * @mixin IdeHelperFeatureFlag
 */
class FeatureFlag extends Model
{
    protected $fillable = [
        'key',
        'label',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (FeatureFlag $flag): void {
            // Always sync current is_active to Pennant (null scope) so the toggle applies globally.
            if ($flag->is_active) {
                Feature::for(null)->activate($flag->key);
            } else {
                Feature::for(null)->deactivate($flag->key);
            }
        });
    }
}
