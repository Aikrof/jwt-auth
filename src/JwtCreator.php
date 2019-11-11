<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth;

use Carbon\Carbon;
use Aikrof\JwtAuth\JwtConfig\JwtConfig;
use Aikrof\JwtAuth\Exception\InvalidTTLException;
use Aikrof\JwtAuth\Exception\JWTException;
use Aikrof\JwtAuth\Parser\EncodeJwt;
use Aikrof\JwtAuth\Hash\HashAlg;
use Illuminate\Support\Str;

trait JwtCreator
{
    /**
     * JSON Web Token time to live.
     *
     * @param int $ttl
     */
    protected $ttl;

    /**
     * Refresh JSON Web Token time to live.
     *
     * @param int $refresh_ttl
     */
    protected $refresh_ttl;

    /**
     * JSON Web Token identifier.
     *
     * @param String $jti
     */
    protected $jti;

    /**
     * Create the JSON Web Token and refresh token.
     *
     * @param void
     *
     * @return array
     *
     * @throws JWTException
     */
    public function token()
    {
        $token = $this->createJWTToken();
        $refresh = $this->createRefreshToken([], explode('.', $token)[2]);

        if (JwtConfig::getConfig('jti'))
            $this->setCookie();

        return ([
            'token' => $token,
            'refresh' => $refresh
        ]);
    }

    /**
     * Create the JSON Web Token.
     *
     * @param array $header
     * @param array $payload
     *
     * @return String $token
     *
     * @throws JWTException
     */
    public function createJWTToken($header = [], $payload = [])
    {
        $header = $header ?: $this->generateHeader();
        $payload = $payload ?: $this->generatePayload();

        $base64UrlHeader = EncodeJwt::base64Url_encode(
            json_encode($header)
        );
        $base64UrlPayload = EncodeJwt::base64Url_encode(
            json_encode($payload)
        );
        $base64UrlSignature = $this->generateSignature(
            $header['alg'], $base64UrlHeader, $base64UrlPayload, 'secret'
        );

        return (
            $base64UrlHeader . '.' .
            $base64UrlPayload . '.' .
            $base64UrlSignature
        );
    }

    /**
     * Create the refresh JSON Web Token.
     *
     * @param array $header
     * @param String $tokenSignature
     *
     * @return String $refreshToken
     *
     * @throws JWTException
     */
    public function createRefreshToken($header = [], String $tokenSignature)
    {
        $header = $header ?: $this->generateHeader();
        $payload = $this->generateRefreshPayload($tokenSignature);

        $base64UrlHeader = EncodeJwt::base64Url_encode(
            json_encode($header)
        );
        $base64UrlPayload = EncodeJwt::base64Url_encode(
          json_encode($payload)
        );
        $base64UrlResreshSignature = $this->generateSignature(
            $header['alg'], $base64UrlHeader, $base64UrlPayload, 'refresh'
        );

        return (
            $base64UrlHeader . '.'.
            $base64UrlPayload .'.'.
            $base64UrlResreshSignature
        );
    }

    /**
     * Generate new token HEADER.
     *
     * @param void
     *
     * @throws JWTException
     * @return array
     */
    protected function generateHeader()
    {
       return ([
            'typ' => "JWT",
            'alg' => $this->getAlg()
        ]);
    }

    /**
     * Generate new token PAYLOAD.
     *
     * @param void
     *
     * @throws JWTException
     * @return array
     */
    protected function generatePayload()
    {
        $iss = JwtConfig::getConfig('iss');
        if (empty($iss))
            throw new JWTException('Could not create JWT PAYLOAD `iss` not found in config/jwt');
        $ttl = $this->ttl ?: JwtConfig::getConfig('ttl');
        if (empty($ttl))
            $ttl = 10080;
        $this->ttl = $ttl;

        $payload = [
            'iss' => $iss,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes($ttl)->timestamp,
            'sub' => $this->id,
        ];

        if (JwtConfig::getConfig('jti')){
            $this->jti = Str::random(60);
            $payload['jti'] = $this->generateJwtId($this->jti);
        }

        $require_payloads = JwtConfig::getConfig('require_payloads');
        $user = $this->toArray();

        foreach ($require_payloads as $key){
            if (array_key_exists($key, $user)){
                $payload[$key] = $user[$key];
            }
        }

        return ($payload);
    }

    /**
     * Generate new refresh token PAYLOAD.
     *
     * @param String $tokenSignature
     *
     * @return array
     */
    protected function generateRefreshPayload(String $tokenSignature)
    {
        $refresh_ttl = JwtConfig::getConfig('refresh_ttl');

        if (empty($refresh_ttl))
            $this->refresh_ttl = $this->refresh_ttl ?: $this->ttl * 2;
        else
            $this->refresh_ttl = $refresh_ttl;

        return ([
            'token' => $tokenSignature,
            'exp' => Carbon::now()->addMinutes($this->refresh_ttl)->timestamp,
        ]);
    }

    /**
     * Generate JSON Web Token SIGNATURE.
     *
     * @param String $alg
     * @param String $base64UrlHeader
     * @param String $base64UrlPayload
     *
     * @return String $token
     *
     * @throws JWTException
     */
    protected function generateSignature(String $alg, String $base64UrlHeader, String $base64UrlPayload, String $key)
    {
        $secret = $this->getSecret($key);

        $signature = (new HashAlg($alg))->make($base64UrlHeader, $base64UrlPayload, $secret);

        return (
            EncodeJwt::base64Url_encode($signature)
        );
    }

    /**
     * Generate JSON Web Token id.
     *
     * @param String $jwt_id
     * @param String $alg
     *
     * @return String $token_id
     *
     * @throws JWTException
     */
    protected function generateJwtId(String $jwt_id, String $alg = null)
    {
        $secret = $this->getSecret('secret');
        $alg = $alg ?: $this->getAlg();

        return (
            (new HashAlg($alg))->hash($jwt_id, $secret, false)
        );
    }

    /**
     * Get param from config/jwt.php by key.
     *
     * @param String $key
     *
     * @return String $param
     *
     * @throws JWTException
     */
    protected function getSecret(String $key)
    {
        $param = JwtConfig::getConfig($key);

        if (empty($param)){
            throw new JWTException('Could not create token, field "'.$key.'" is missing in config/jwt');
        }
        return ($param);
    }

    /**
     * Get hash algorithm from config/jwt.php.
     *
     * @return String $alg
     *
     * @throws JWTException
     */
    protected function getAlg()
    {
        $alg = JwtConfig::getConfig('algo');
        if (empty($alg)){
            throw new JWTException('Could not create JWT HEADER, field "algo" is missing in config/jwt');
        }

        return ($alg);
    }

    /**
     * Set token ttl in minutes.
     *
     * @param $time
     *
     * @throws InvalidTTLException when invalid token time to live.
     * @return \Aikrof\JwtAuth\JwtCreator
     */
    public function setTtl($time)
    {
        if (!is_numeric($time) || $time <= 0){
            throw new InvalidTTLException($time);
        }

        $this->ttl = $time;

        return ($this);
    }

    /**
     * Set refresh token ttl in minutes.
     *
     * @param $time
     *
     * @throws InvalidTTLException when invalid token time to live.
     * @return \Aikrof\JwtAuth\JwtCreator
     */
    public function setRefreshTtl($time)
    {
        if (!is_numeric($time) || $time <= 0){
            throw new InvalidTTLException($time);
        }

        $this->refresh_ttl = $time;

        return ($this);
    }

    /**
     * Get current time by Unix timestamp.
     *
     * @return int $time
     */
    public function getCurrentTime()
    {
        return (Carbon::now()->timestamp);
    }

    /**
     * Get token time to live.
     *
     * @return int $ttl
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Get refresh token time to live.
     *
     * @return int $refresh_ttl
     */
    public function getRefreshTtl()
    {
        return $this->refresh_ttl;
    }

    /**
     * Get token expires at.
     *
     * @return int $expires_at
     */
    public function getExpTtl()
    {
        return Carbon::now()->addMinutes($this->ttl)->timestamp;
    }

    /**
     * Get refresh token expires at.
     *
     * @return int $expires_at
     */
    public function getRefreshExpTtl()
    {
        return Carbon::now()->addMinutes($this->refresh_ttl)->timestamp;
    }

    /**
     * Get refresh token expires at.
     *
     * @param int $date1
     * @param int $date2
     *
     * @return int $diff
     */
    public function getDiffInMinutes(int $date1, int $date2)
    {
        $from = Carbon::parse(Carbon::createFromTimestamp($date1)->toDateTimeString());
        $to = Carbon::parse(Carbon::createFromTimestamp($date2)->toDateTimeString());
        $diff = $to->diffInMinutes($from);

        return ($diff);
    }

    /**
     * Set cookie to protect token.
     *
     * @return void
     */
    protected function setCookie()
    {
        $ssl = !!JwtConfig::getConfig('SSL');

        setcookie(
            'secure',
            $this->jti,
            Carbon::now()->addMinutes($this->ttl)->timestamp,
            "/",
            "",
            $ssl,
            true
        );
    }

    /**
     * Unset cookie.
     *
     * @return void
     */
    public function unsetCookie()
    {
        $ssl = !!JwtConfig::getConfig('SSL');

        if (!empty($_COOKIE['secure'])){
            unset($_COOKIE['secure']);
            setcookie(
                'secure',
                null,
                Carbon::now()->timestamp,
                "/",
                "",
                $ssl,
                true
            );
        }
    }
}
