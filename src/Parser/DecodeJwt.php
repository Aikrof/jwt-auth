<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Parser;

class DecodeJwt
{
    public static function base64Url_decode(String $token)
    {
        return (
            base64_decode(
                str_pad(
                    strtr($token, '-_', '+/'),
                    strlen($token) % 4,
                    '=',
                    STR_PAD_RIGHT)
            )
        );
    }
}
