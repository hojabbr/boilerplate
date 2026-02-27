@php
    $setting = \App\Core\Models\Setting::site();
    $logoUrl = $setting->getFirstMediaUrl('app_logo') ?: asset('favicon/favicon.svg');
@endphp
<img src="{{ $logoUrl }}" alt="{{ $setting->company_name ?: config('app.name') }}" class="h-8 w-auto">
