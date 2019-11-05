<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Exception;

class InvalidJWTExeption extends JWTException
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'Invalid JSON Web Token structure';
}
