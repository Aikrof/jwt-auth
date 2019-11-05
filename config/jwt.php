<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Issuer (who created and signed this token)
    |--------------------------------------------------------------------------
    */
    'iss' => env('APP_NAME', "My app"),

    /*
    |--------------------------------------------------------------------------
    | JSON Web Token secret
    |--------------------------------------------------------------------------
    | Will be used to create token verify signature.
    */
    'secret' => env('JWT_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | JSON Web Token refresh secret
    |--------------------------------------------------------------------------
    | Will be used to create a refresh token verify signature.
    */
    'refresh' => env('JWT_REFRESH_KEY'),

    /*
    |--------------------------------------------------------------------------
    | JSON Web Token time to live
    |--------------------------------------------------------------------------
    | Specify the length of time (in minutes) that the token will be valid for.
    | If this set to null, will be take a default value.
    | Defaults to 1 week.
    */
    'ttl' => env('JWT_TTL', null),

    /*
    |--------------------------------------------------------------------------
    | Refresh Token time to live
    |--------------------------------------------------------------------------
    | Specify the length of time (in minutes) that the token will be valid for.
    | If this set to null, will be take a default value.
    | Defaults to 2 weeks.
    */
    'refresh_ttl' => env('JWT_REFRESH_TTL', null),

    /*
    |--------------------------------------------------------------------------
    | JSON Web Token hashing algorithm
    |--------------------------------------------------------------------------
    | Specify the hashing algorithm that will be used to sign the token.
    | At version 1.0.0 supports ["HS256", "HS384, "HS512"]
    */
    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | JSON Web Token identifier
    |--------------------------------------------------------------------------
    | If value set to true, use cookie to protect token against theft.
    */
    'jti' => false,

    /*
    |--------------------------------------------------------------------------
    | SSL certificate
    |--------------------------------------------------------------------------
    | Set value to true if you have SSL certificate
    */
    'SSL' => false,

    /*
    |--------------------------------------------------------------------------
    | Required Payloads
    |--------------------------------------------------------------------------
    | Specify the required payloads that must exist in any token.
    | If key does not exist in your user table it will be ignored.
    */
    'require_payloads' => [],
];

