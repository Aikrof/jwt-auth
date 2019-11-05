<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\JwtConfig;

interface FileConfig
{
    /**
     * Get data from config/jwt.php
     *
     * @param String $key
     *
     * @return array|string from config/jwt.php
     */
    public static function getConfig($key);
}
