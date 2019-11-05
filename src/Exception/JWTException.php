<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Exception;

use Exception;

class JWTException extends Exception
{
    /**
     * Shell of JSON Web Token Exception
     */

    public function __construct($name = null)
    {
        if (!empty($name))
            $this->message .= ": " . $name;

        parent::__construct($this->message);
    }
}
