<?php


namespace Core\Http\ResponseComplements;

use Core\Foundation\Traits\Http\httpResponses;
use Core\Foundation\Traits\Http\responseMessages;

class redirectResponse
{

    use httpResponses, responseMessages;

    private string $location = '/';
    private ?array $message;
    private int $code;

    public function __construct(string $location = '/', ?array $message = null, int $code = 200)
    {
        $this->location = $location;
        $this->message = (array) $message;
        $this->code = $code;
        $this->proccessLocation();
    }

    public function __destruct()
    {
        $this->redirect($this->location, $this->code)->with(
            key($this->message) ?? '',
            array_values($this->message)[0] ?? ''
        );
    }

    public function proccessLocation(): void
    {
        if ($this->location === 'back') $this->location = \Core\Http\Server::referer();
    }
}


/*
class RedirectResponse extends \Core\Http\Response
{

    private string $location = '/';

    public function __construct(string $location = '/', ?array $message = null, int $code = 200)
    {
        $this->location = $location;
        $this->proccessLocation();

        $response = new parent(null, $code, ['location' => $this->location]);

        $response->with(
            key($message) ?? '',
            array_values($message) ?? ''
        );
    }

    public function proccessLocation(): void
    {
        if ($this->location === 'back') $this->location = \Core\Http\Server::referer();
    }
}
*/