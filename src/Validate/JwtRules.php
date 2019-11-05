<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Validate;

trait JwtRules
{
    /**
     * Need to be in JSON Web Token PAYLOAD.
     *
     * @var array
     */
    protected $need = [
        'iss',
        'iat',
        'exp',
        'sub'
    ];

    /**
     * Algorithms that this library supports.
     *
     * @var array
     */
    protected $algorithms = [
        'HS256' => 'sha256',
        'HS384' => 'sha384',
        'HS512' => 'sha512',
    ];

    protected function checkAlgorithm(String $alg)
    {
        return (
            array_key_exists($alg, $this->algorithms)
        );
    }

    protected function needPayloadsData($payload)
    {
        foreach ($this->need as $key){
            if (!array_key_exists($key, $payload))
                return (false);
        }
        return (true);
    }
}
