<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Hash;

class HashAlg extends Hash
{
    /**
     * Hash JSON Web Token.
     *
     * @param String json $jwt_header
     * @param String json $jwt_payload
     * @param String $secret
     *
     * @return String
     */
    public function make(String $base64UrlHeader, String $base64UrlPayload, String $secret)
    {
        return (
            $this->hash($base64UrlHeader . "." . $base64UrlPayload, $secret)
        );
    }

    /**
     * Hash JSON Web Token.
     *
     * @param String $data
     * @param String $secret
     * @param bool $raw_output
     *
     * @return String
     */
    public function hash(String $data, String $secret, bool $raw_output = true)
    {
        return (
            hash_hmac(
                $this->alg,
                $data,
                $secret,
                $raw_output
            )
        );
    }
}
