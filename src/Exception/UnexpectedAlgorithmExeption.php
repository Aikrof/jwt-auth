<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Exception;

class UnexpectedAlgorithmExeption extends JWTException
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'Unexpected hash algorithm';
}
