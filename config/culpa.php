<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
use Illuminate\Support\Facades\Auth;

return array(
    'users' => array(
        /*
         * Retrieve the authenticated user's ID
         * @return int|null User ID, or null if not authenticated
         */
        'active_user' => function () {
            return Auth::check() ? Auth::user()->id : null;
        },
        /*
         * Class name of the user object to relate to
         * @var string
         */
        'classname' => 'App\User',
    ),
    'default_fields' => array(
        'created' => 'created_by',
        'updated' => 'updated_by',
        'deleted' => 'deleted_by',
    )
);
