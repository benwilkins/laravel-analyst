<?php

return [
    /*
     * Path to the client secret json file.
     */
    'google_account_credentials_json' => storage_path('app/laravel-analyst/Google/account-credentials.json'),
    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 24,
    /*
     * The directory where the underlying Google_Client will store it's cache files.
     */
    'cache_location' => storage_path('app/laravel-analyst/'),
    /*
     * The directory where custom internal metrics are stored.
     */
    'custom_metric_location' => '/app/Metrics/',
    /*
     * Default data client
     */
    'default_client' => 'internal',
];