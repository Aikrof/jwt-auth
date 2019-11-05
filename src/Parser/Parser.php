<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Parser;

use Aikrof\JwtAuth\Exception\InvalidJWTExeption;

class Parser
{
    /**
     * The incoming JWT token.
     *
     * @var String $token
     */
    private $token;

    /**
     * The incoming COOKIE.
     *
     * @var String $token
     */
    private $cookie;

    /**
     * The JWT token headers.
     *
     * @var array
     */
    private $header;

    /**
     * The JWT token payload.
     *
     * @var array
     */
    private $payload;

    /**
     * The JWT token signature.
     *
     * @var String
     */
    private $signature;

    /**
     * Initializes the object.
     *
     * @param String $token
     * @param String $cookie
     */
    public function __construct(String $token, String $cookie = null)
    {
        $this->token = $token;
        $this->cookie = $cookie;
    }

    public function __get(String $name)
    {
        return ($this->$name);
    }

    /**
     * Parse JWT string.
     *
     * @return object Parser
     *
     * @throws InvalidJWTExeption when JWT have invalid structure
     */
    public function parseJwt()
    {
        $jwt = explode('.', $this->token);

        if (count($jwt) != 3)
            throw new InvalidJWTExeption();

        $this->parseDataJwt($jwt);

        return ($this);
    }

    /**
     * Parse incoming JWT token data.
     *
     * @param array $jwt
     *
     * @return void
     */
    public function parseDataJwt(array $jwt)
    {
        $this->header = json_decode(DecodeJwt::base64Url_decode($jwt[0]));
        $this->payload = json_decode(DecodeJwt::base64Url_decode($jwt[1]));
        $this->signature = $jwt[2];
    }

    /**
     * Get sub from incoming JWT token.
     *
     * @return int $sub (id)
     */
    public function getSub()
    {
        return ($this->payload->sub);
    }
}
