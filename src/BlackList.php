<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth;

class BlackList
{
    /**
     * Find token in blacklist.
     *
     * @param  String $token
     *
     * @return bool
     */
    public static function find(String $token)
    {
        return (
            !!BlackListModel::find($token)
        );
    }

    /**
     * Add token to blacklist.
     *
     * @param  String  $token
     * @param  int  $exp
     *
     * @return void
     */
    public static function add(String $token, int $exp)
    {
        BlackListModel::create([
            'token' => $token,
            'exp' => $exp
        ]);
    }
}
