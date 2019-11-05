<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth;

use Illuminate\Database\Eloquent\Model;

class BlackListModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'black_lists_jwt';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = null;

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

    protected $primaryKey = 'token';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'exp'
    ];
}
