<?php

namespace Core\Client\Authentification;

use Core\Config\Support\interactsWithAuthConfig;
use Core\Http\Cookie;
use Core\Http\Persistent;
use Exception;

/**
 * User will be redirected to a custom location if provided
 * by adding the property redirectGuestTo and giving it a value,
 * otherwise user will be redirected to route /user/login
 */

trait guestsNotAllowed
{

    use interactsWithAuthConfig;

    private object $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = $this->getAuthConfig();
        $this->mustBeAuthenticated();
    }

    private function mustBeAuthenticated(): void
    {

        $this->evaluateCookieExpiration();

        if (!Persistent::get($this->config->session['key'])) {
            
            new \Core\Http\ResponseComplements\redirectResponse(
                $this->redirectGuestTo ?? '/user/login',
                ['error' => 'You need to be identified'],
                500
            );

            exit;

        }
    }

    private function evaluateCookieExpiration(): void
    {
        if (!Cookie::has($this->config->cookie['name'])) {
            Persistent::destroy($this->config->session['key']);
        }
    }
}
