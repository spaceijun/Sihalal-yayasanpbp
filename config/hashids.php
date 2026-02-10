<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        'main' => [
            'salt' => 'xK9mP2qL5vN8wB3cF6hJ1dG4kM7nR0tSyU7iO4pL1m',
            'length' => 15,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyz0123456789'
        ],
        'alternative' => [
            'salt' => 'aB3cD5eF7gH9iJ1kL3mN5oP7qR9sT1uV',
            'length' => 20,
            'alphabet' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
        ],
    ],

];
