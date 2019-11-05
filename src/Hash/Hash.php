<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Hash;

use Aikrof\JwtAuth\Validate\JwtRules;

abstract class Hash
{
    use JwtRules;

    protected $alg;

    /**
     * Set the hash algorithm.
     *
     * @param String $alg
     */
    public function __construct(String $alg)
    {
        $this->alg = $this->algorithms[$alg];
    }

    /**
     * Hash JSON Web Token.
     *
     * @param String json $jwt_header
     * @param String json $jwt_payload
     * @param String $secret
     *
     * @return String
     */
    public abstract function make(String $base64UrlHeader, String $base64UrlPayload, String $secret);
}
