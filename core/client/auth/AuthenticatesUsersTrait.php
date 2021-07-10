<?php

namespace Core\Client\Authentification;

use Core\Http\Cookie;
use Core\Http\Persistent;
use Core\Http\ResponseComplements\redirectResponse;
use Core\Support\Formating\MsgParser;

trait AuthenticatesUsers
{

    public function auth(string $user, string $password)
    {
        $user = $this->model::find([$this->config->auth_keys['user'] => $user]);

        if (is_object($user) && password_verify($password, $user->password)) {
            $this->setSession($user);

            return $this->onSuccess($user->name)->withCookie(

                new Cookie(
                    $this->config->cookie['name'],
                    $this->config->cookie['value'],
                    time() + $this->config->cookie['expires'],
                    $this->config->cookie['path'],
                    null,
                    false,
                    true
                )

            );
        }

        return $this->onError();
    }

    private function setSession(object $user_data)
    {
        Persistent::create($this->config->session['key'], $user_data);
    }

    public function logout()
    {
        Persistent::destroy($this->config->session['key']);
        Cookie::remove($this->config->cookie['name']);
        return new redirectResponse('/');
    }

    private function onSuccess(string $name)
    {
        return new redirectResponse(
            '/',
            [
                'success' => MsgParser::format(
                    $this->config->response_msgs['success'],
                    $name
                )
            ]
        );
    }

    private function onError()
    {
        return new redirectResponse(
            'back',
            [
                'error' => MsgParser::format(
                    $this->config->response_msgs['error']
                )
            ]
        );
    }
}
