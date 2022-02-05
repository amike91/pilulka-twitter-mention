<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Application-only authentication token (Bearer token)
    |--------------------------------------------------------------------------
    |
    | Token used for authentication in Twitter API v2. Works only on endpoints
    | that DO NOT require user's OAuth 2 token they were called on behalf of,
    | such as Recent Tweets.
    |
    */

    'bearer_token' => env('TWITTER_API_BEARER_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL of the Twitter API v2
    |--------------------------------------------------------------------------
    |
    | Base URL of the Twitter API v2 to call individual endpoints on.
    |
    */

    'base_api_url' => env('TWITTER_API_BASE_URL', ''),
];
