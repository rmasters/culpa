<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
return array(
    'users' => array(
        /**
         * Uncomment the lines below if you want to overwrite the user source
         *
         * @note:
         * enabling this closure breaks laravel's feature to cache config files
         * The default (commented) option should work for you if you are using the default Auth provider.
         * @see: https://github.com/laravel/framework/issues/9625
         * @return int|null User ID, or null if not authenticated
         *
         * 'active_user' => function () {
         * return Auth::check() ? Auth::user()->id : null;
         * },
         */


        /**
         * Class name of the user object to relate to
         * @var string
         */
        'classname' => 'App\User',


    ),
    /**
     * The default datasource fields
     */
    'default_fields' => array(
        'created' => 'created_by',
        'updated' => 'updated_by',
        'deleted' => 'deleted_by',
    )
);
