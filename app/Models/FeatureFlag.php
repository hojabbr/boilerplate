<?php

namespace App\Models;

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
            if (! $flag->wasChanged('is_active')) {
                return;
            }
            if ($flag->is_active) {
                Feature::activate($flag->key);
            } else {
                Feature::deactivate($flag->key);
            }
        });
    }
}
