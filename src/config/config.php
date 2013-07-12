<?php

use Illuminate\Support\Facades\Auth;

return array(
    'users' => array(
        /**
         * Retrieve the authenticated user's ID
         * @return int|null User ID, or null if not authenticated
         */
        'active_user' => function() {
            return Auth::check() ? Auth::user()->id : null;
        },

        /**
         * Class name of the user object to relate to
         * @var string
         */
        'classname' => 'User',
    ),
);
