<?php

namespace Core\Support\Formating;

class MsgParser
{

    private string $identifier = '/:+\w+/';
    private string $message;
    private array $values;

    public function __construct(string $message, ...$values)
    {
        $this->message = $message;
        $this->values = $values;
    }

    public function perform(): string
    {
        preg_match_all($this->identifier, $this->message, $matches);
        return str_ireplace($matches[0], $this->values[0], $this->message);
    }

    public static function format(string $message, ...$values): string
    {
        $self = new self($message, $values);
        return $self->perform();
    }
}
