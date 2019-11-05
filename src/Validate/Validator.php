<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Validate;

use Aikrof\JwtAuth\BlackList;
use Aikrof\JwtAuth\JwtCreator;
use Aikrof\JwtAuth\JwtConfig\JwtConfig;
use Aikrof\JwtAuth\Parser\Parser;
use Aikrof\JwtAuth\Exception\JWTException;
use Aikrof\JwtAuth\Exception\UnexpectedAlgorithmExeption;
use Aikrof\JwtAuth\Exception\InvalidJWTExeption;
use Aikrof\JwtAuth\Exception\InvalidKeyException;
use Aikrof\JwtAuth\Exception\InvalidJWTPayloadExeption;

class Validator
{
    use JwtRules, JwtCreator;

    /**
     * The data Parser
     *
     * @var object Parser $parser
     */
    private $parser;

    /**
     * Validate JWT token.
     *
     * @param Parser  $parser
     *
     * @return void
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Validate JWT token
     *
     * @return Boolean
     *
     * @throws InvalidKeyException
     * @throws InvalidJWTExeption
     * @throws UnexpectedAlgorithmExeption
     * @throws InvalidJWTPayloadExeption
     * @throws JWTException
     */
    public function verifyJwt()
    {
        try{
            $this->validateHeader();
            $this->validatePayload();
        }catch(JWTException $e){
            throw new JWTException('JSON Web Token mismatch: ' . $e->getMessage());
        }

        if (BlackList::find($this->parser->signature)){
            return (false);
        }

        $validToken = $this->getValidateToken();

        return ($this->compareTokens($this->parser->token, $validToken));
    }

    /**
     * Validate Cookie
     *
     * @return Boolean
     *
     * @throws JWTException
     */
    public function validateCookie()
    {
        if (JwtConfig::getConfig('jti')) {
            if ((!$this->parser->cookie) ||
                (!$jwt_id = $this->parser->payload->jti)) {
                return (false);
            }

            $id = $this->generateJwtId($this->parser->cookie, $this->parser->header->alg);

            return ($id === $jwt_id);
        }

        return (true);
    }

    /**
     * Validate JWT token.
     *
     * @return void
     *
     * @throws InvalidJWTExeption when JWT have invalid structure
     * @throws UnexpectedAlgorithmExeption when the algorithm does not exist
     */
    protected function validateHeader()
    {
        $header = $this->parser->header;

        if (empty($header->typ) ||
            $header->typ !== 'JWT' ||
            empty($header->alg)){
            throw new InvalidJWTExeption;
        }
        else if (!$this->checkAlgorithm($header->alg)){
            throw new UnexpectedAlgorithmExeption($header->alg);
        }
    }

    /**
     * Validate JWT token.
     *
     * @return void
     *
     * @throws InvalidJWTExeption when JWT have invalid PAYLOAD structure what defined in config/jwt.php
     * @throws InvalidJWTPayloadExeption when JWT PAYLOAD have invalid value
     */
    protected function validatePayload()
    {
        $payload = $this->parser->payload;

        if (!$this->needPayloadsData($payload))
            throw new InvalidJWTExeption;

        if ($payload->iss !== JwtConfig::getConfig('iss')){
            throw new InvalidJWTPayloadExeption($payload->iss);
        }

        if (!is_numeric($payload->sub)) {
            throw new InvalidJWTPayloadExeption($payload->sub);
        }
    }

    /**
     * Get new JSON Web Token with request header and payload.
     *
     * @param void
     *
     * @return String $token
     *
     * @throws JWTException
     */
    protected function getValidateToken()
    {
        return (
            $this->createJWTToken(
                (array)$this->parser->header,
                (array)$this->parser->payload
            )
        );
    }

    /**
     * Compare two tokens.
     *
     * @param String $token1
     * @param String $token2
     *
     * @return bool
     */
    public function compareTokens(String $token1, String $token2)
    {
        return ($token1 === $token2);
    }

    /**
     * Check JWT Exp time.
     *
     * @param void
     *
     * @return Boolean
     */
    public function checkTokenExpTime()
    {
        $current = $this->getCurrentTime();
        $expiresAt = $this->parser->payload->exp;

        return ($current > $expiresAt);
    }
}
