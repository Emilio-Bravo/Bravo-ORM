<?php

namespace Core\Http\RequestComplements;

trait handlesRequestHeaders
{

    private $request_headers;

    public function getHttpHeaders(): object
    {
        foreach ($this->request_headers as $key => $value) {
            !preg_match('/HTTP/', $key) ?: $expected[$key] = $value;
        }
        return (object) $expected;
    }

    public function header(string $header_name)
    {
        return $this->request_headers->$header_name ?? null;
    }

    public function getHttpHeader(string $header_name)
    {
        return $this->request_headers->{"HTTP_$header_name"} ?? null;
    }

    public function hasHeader(string $header_name): bool
    {
        return $this->getHttpHeader($header_name) != null;
    }

    private function parseRequestHeaders(): void
    {
        foreach (\Core\Http\Server::server() as $key => $value) {
            $sanitized_key = preg_replace('/[-]/', '_', $key);
            $this->request_headers[$sanitized_key] = $value;
        }
        $this->request_headers = (object) $this->request_headers;
    }

    public function getHeaders(): object
    {
        return $this->request_headers;
    }
}
