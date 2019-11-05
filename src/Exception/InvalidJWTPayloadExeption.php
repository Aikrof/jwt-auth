<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Exception;

class InvalidJWTPayloadExeption extends JWTException
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'Undefined JWT PAYLOAD field';
}
