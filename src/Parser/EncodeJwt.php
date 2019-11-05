<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Parser;

class EncodeJwt
{
    public static function base64Url_encode(String $data)
    {
        return rtrim(
            str_replace(
                '=',
                '',
                strtr(base64_encode($data), '+/', '-_')
            )
        );
    }
}
