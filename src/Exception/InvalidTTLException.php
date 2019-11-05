<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Exception;

class InvalidTTLException extends JWTException
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'Invalid token time to live';
}
