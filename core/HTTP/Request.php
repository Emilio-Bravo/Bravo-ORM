<?php

namespace Core\Http;

use Core\Http\RequestComplements\handlesRequestHeaders;
use Core\Support\Files\HandlesImages;
use Core\Support\Files\HandlesRequestFiles;
use Core\Support\Files\handlesUploadedFiles;

class Request
{

    use HandlesRequestFiles,
        HandlesImages,
        handlesUploadedFiles;

    use handlesRequestHeaders;

    private ?array $input = [];

    public function __construct()
    {
        $this->sanitizeRequest();
        $this->parseRequestHeaders();
    }

    private function sanitizeRequest(): void
    {
        switch (Server::method()) {
            case 'GET':
                $this->input = \Core\Support\HttpSanitizer::sanitize_get();
                break;
            case 'POST':
                $this->input = \Core\Support\HttpSanitizer::sanitize_post();
                break;
        }
    }

    public function all(): array
    {
        return (array) $this->input;
    }

    public function except(...$inputs): array
    {
        foreach ($this->input as $key => $value) {
            if (!in_array($key, $inputs)) $expected[$key] = $value;
        }
        return (array) $expected;
    }

    public function input(string $input)
    {
        return $this->input[$input];
    }

    public function setInputValue(string $input, $value): void
    {
        $this->input[$input] = $value;
    }

    public function getMtehod(): string
    {
        return Server::method();
    }
}
