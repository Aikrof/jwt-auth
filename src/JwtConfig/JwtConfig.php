<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\JwtConfig;

use Illuminate\Support\Facades\Config;

class JwtConfig implements FileConfig
{
    /**
     * Get data from config/jwt.php
     *
     * @param String $key
     *
     * @return array|string from config/jwt.php
     */
    public static function getConfig($key)
    {
        $struct = Config::get('jwt.'.$key);

        return ($struct);
    }
}
