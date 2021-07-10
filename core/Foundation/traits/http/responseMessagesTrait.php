<?php

namespace Core\Foundation\Traits\Http;

use Core\Http\Cookie;
use Core\Support\Flash;

trait responseMessages
{
    public function withSuccess(string $message): self
    {
        Flash::create('success', $message);
        return $this;
    }

    public function withError(string $message): self
    {
        Flash::create('error', $message);
        return $this;
    }

    public function with(string $key, $value): self
    {
        Flash::create($key, $value);
        return $this;
    }

    public function withCookie(Cookie $cookie): self
    {
        if (method_exists($this, 'setHeader')) $this->setHeader('Set-Cookie', $cookie, false);

        return $this;
    }

    public function withOutCookie(string $name): self
    {
        if (Cookie::has($name)) Cookie::remove($name);
        return $this;
    }
}
