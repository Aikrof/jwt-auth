<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Exception;

class InvalidKeyException extends JWTException
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'Invalid config/jwt Key';
}
