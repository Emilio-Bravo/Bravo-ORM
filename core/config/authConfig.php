<?php

return [

    'session' => [
        'key' => 'user'
    ],

    'response_msgs' => [
        'error' => 'Incorrect username and / or password',
        'user_error' => ':user is already in use',
        'success' => 'Welcome :name'
    ],

    'auth_keys' => [
        'user' => 'email',
        'password' => 'password'
    ],

    'cookie' => [
        'name' => 'user_session',
        'value' => \Core\Support\Crypto::generateToken(),
        'path' => '/',
        'expires' => 3600 //one hour
    ]
];
