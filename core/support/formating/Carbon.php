<?php

namespace Core\Support\Formating;

class Carbon extends \DateTime
{
    public function __toString(): string
    {
        return $this->format(self::ISO8601);
    }

    public function __invoke(string $format)
    {
        return $this->format($format);
    }

    public function getAge(string $time = 'now'): string
    {
        return $this->diff($time)->format('%y');
    }
}
