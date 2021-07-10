<?php

namespace Core\Http;

class Router
{

    private string $uri;
    private string $method;
    public array $routes = [];

    public function __destruct()
    {
        $this->resolve();
    }

    public function resolve()
    {
        $this->setRouterInfo();
        $callback = $this->routes[$this->method][$this->uri];
        return $this->handle($callback);
    }

    public function handle($callback, ...$args)
    {
        if (is_array($callback)) {
            $ob = new $callback[0];
            return call_user_func([$ob, $callback[1]], empty($args) ? new Request : $args);
        }
        return call_user_func($callback);
    }

    public function setRouterInfo(): void
    {
        $this->method = \Core\Http\Server::method();
        $this->uri = \Core\Http\Server::uri();
    }

    public function get(string $path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }
}
