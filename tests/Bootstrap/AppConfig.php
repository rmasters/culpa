<?php

namespace Culpa\Tests\Bootstrap;

return array(
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
