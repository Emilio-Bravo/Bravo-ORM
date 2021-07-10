<?php

namespace Core\Foundation;

use Core\Http\Response;
use Core\Client\View;

class Controller
{
    protected View $view;
    protected Response $response;

    public function __construct()
    {
        $this->view = new View;
        $this->response = new Response;
    }

    protected function render($view, array $vars = [], $code = 200): void
    {
        new Response($this->view->render($view, $vars), $code);
        \Core\Support\Flash::enable(); //Enbale flash sessions
    }

    protected function redirect(string $location = '/'): Response
    {
        return $this->response->redirect($location);
    }

    protected function back(): Response
    {
        return $this->redirect(\Core\Http\Server::referer());
    }

    protected function validate(\Core\Http\Request $request, array $data_patern): \Core\Support\Validator
    {
        $validator = new \Core\Support\Validator;
        return $validator->validate($request, $data_patern);
    }
}
