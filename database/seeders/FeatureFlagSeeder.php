<?php

namespace Database\Seeders;

use App\Core\Models\FeatureFlag;
use Illuminate\Database\Seeder;
use Laravel\Pennant\Feature;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Seed feature_flags from config and sync is_active from Pennant.
     */
    public function run(): void
    {
        $toggleable = config('features.toggleable', []);

        foreach ($toggleable as $key => $label) {
            FeatureFlag::updateOrCreate(
                ['key' => $key],
                [
                    'label' => $label,
                    'is_active' => (bool) Feature::for(null)->active($key),
                ]
            );
        }
    }
}
