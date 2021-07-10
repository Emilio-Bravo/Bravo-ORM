<?php

namespace Core\Client\Authentification;

use Core\Http\ResponseComplements\redirectResponse;
use Core\Support\Crypto;
use Core\Support\Formating\MsgParser;

trait createsUsers
{

    public function newUser(array $data)
    {

        $password = $this->config->auth_keys['password'];
        $user = $this->config->auth_keys['user'];

        $data[$password] = Crypto::cryptoPassword($data[$password]);

        if ($this->userIsUnique($data[$user])) $this->model::insert($data);

        else {

            return new redirectResponse(
                'back',
                [
                    'error' => MsgParser::format(
                        $this->config->response_msgs['user_error'],
                        $data[$user]
                    )
                ]
            );
            
        }
    }

    private function userIsUnique(string $user): bool
    {
        return count($this->model::findAll(
            [$this->config->auth_keys['user'] => $user]
        )) < 1;
    }
}
