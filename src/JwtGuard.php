<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth;

use Aikrof\JwtAuth\Validate\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Aikrof\JwtAuth\Parser\Parser;
use Aikrof\JwtAuth\Exception\JWTException;

class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The parser of request token data.
     *
     * @var \Aikrof\JwtAuth\Parser\Parser
     */
    protected $parser;

    /**
     * The parser of request token data.
     *
     * @var bool
     */
    protected $token_exp = true;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (! is_null($this->user)){
            return $this->user;
        }
        else if (empty($this->request->bearerToken())){
            $this->token_exp = false;
            return (null);
        }

        try {
            $this->parser = $this->parseData();

            $validator = new Validator($this->parser);

            if (!$validator->verifyJwt()){
                $this->token_exp = false;
                return (null);
            }
            else if ($validator->checkTokenExpTime()){
                return (null);
            }
            else if (!$validator->validateCookie()){
                $this->token_exp = false;
                return (null);
            }

            $this->user = $this->provider->retrieveById($this->parser->getSub());

            return ($this->user);
        } catch (JWTException $exception) {
            return (null);
        }
    }

    /**
     * Parse request data token.
     *
     * @return object Parser
     *
     * @throws JWTException
     */
    protected function parseData()
    {
        return (
            (new Parser(
                $this->request->bearerToken(),
                $this->request->cookie('secure'))
            )->parseJwt()
        );
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return (!!$this->attempt($credentials, false));
    }

    /**
     * Attempt to authenticate the user using the given credentials and return the token.
     *
     * @param  array  $credentials
     * @param  bool  $attem
     *
     * @return bool|string
     */
    public function attempt(array $credentials = [], $attem = true)
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            if ($attem)
                $this->user = $user;
            return (true);
        }

        return (false);
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  mixed  $user
     * @param  array  $credentials
     *
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Logout the user, this invalidating the token.
     *
     * @return void
     */
    public function logout()
    {
        try{
            $parser = $this->parser ?: $this->parseData();
        }catch (JWTException $exception){
            return;
        }
        BlackList::add($parser->signature, $parser->payload->exp);
        $this->user->unsetCookie();
        $this->user = null;
        return (null);
    }

    /**
     * Refresh old tokens.
     *
     * @return array|null
     */
    public function refresh()
    {
        if (!($refresh = $this->request->header('Authenticate')) ||
            !$this->token_exp){
            return (null);
        }

        $refresh_parser = (new Parser($refresh));
        $refresh_parser->parseDataJwt(explode('.', $refresh));

        $validator = new Validator($refresh_parser);

        if ($validator->checkTokenExpTime() ||
            !$validator->compareTokens(
                $this->parser->signature,
                $refresh_parser->payload->token)){
            return (null);
        }

        BlackList::add(
            $refresh_parser->payload->token,
            $this->parser->payload->exp
        );

        $this->user = $this->provider->retrieveById($this->parser->getSub());

        return (
            $this->user
                ->setTtl(
                    $this->user->getDiffInMinutes(
                        $this->parser->payload->iat,
                        $this->parser->payload->exp
                    )
                )->setRefreshTtl(
                    $this->user->getDiffInMinutes(
                        $this->parser->payload->iat,
                        $refresh_parser->payload->exp
                    )
                )->token()
        );
    }
}
