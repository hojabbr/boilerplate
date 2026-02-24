<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasLabel
{
    case Content = 'Content';
    case Blog = 'Blog';
    case Access = 'Access';
    case Settings = 'Settings';

    public function getLabel(): string
    {
        return match ($this) {
            self::Content => __('Content'),
            self::Blog => __('Blog'),
            self::Access => __('Access'),
            self::Settings => __('Settings'),
        };
    }
}
