<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blog (fallbacks when not overridden in Settings)
    |--------------------------------------------------------------------------
    |
    | posts_per_page: Fallback when Settings > site > Blog > Posts per page is empty.
    | translation_body_chunk_size: Fallback when Settings > site > Blog > Translation
    | body chunk size is empty. Values are cached with the site setting; clear cache
    | via SettingObserver when the site setting is saved/updated/deleted.
    |
    */

    'posts_per_page' => (int) env('BLOG_POSTS_PER_PAGE', 10),

    'translation_body_chunk_size' => (int) env('BLOG_TRANSLATION_BODY_CHUNK_SIZE', 6000),

];
