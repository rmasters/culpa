<?php

namespace Culpa\Tests\Bootstrap;

use Culpa\Facades\Schema;

return array(
    'app' => [
        'aliases' => [
            'Schema' => Schema::class,
        ],
    ],
    'database' => array(
        'default' => 'sqlite',
        'connections' => array(
            'sqlite' => array(
                'database' => ':memory:',
                'driver' => 'sqlite',
                'prefix' => '',
            ),
        ),
    ),
);
