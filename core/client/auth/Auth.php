<?php

namespace Core\Client\Authentification;

use Core\Config\Support\interactsWithAuthConfig;

class Auth
{
    use AuthenticatesUsers, createsUsers;
    use interactsWithAuthConfig;

    private object $model;
    private object $config;

    public function __construct($model)
    {
        $this->model = new $model;
        $this->config = $this->getAuthConfig();
    }

    public static function user()
    {
        return (object) \Core\Http\Persistent::get('user');
    }
}
