<?php

namespace Core\Foundation\Traits\Http;

trait httpResponses
{

    public function statusCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect(string $location = '/', int $code = 200): self
    {
        $this->statusCode($code);
        $this->setHeader('location', $location);
        return $this;
    }

    public function setHeader(string $key, string $value, bool $replace = true, int $code = 200): self
    {
        header("$key: $value", $replace);
        return $this;
    }

    public function removeHeader(string $key): self
    {
        header_remove($key);
        return $this;
    }

    public function addCookie(\Core\Http\Cookie $cookie)
    {
        $this->setHeader('Set-Cookie', $cookie, false);
    }
}
