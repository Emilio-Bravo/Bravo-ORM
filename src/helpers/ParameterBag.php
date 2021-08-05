<?php

namespace Bravo\ORM;

use ArrayObject;

class ParameterBag extends ArrayObject
{
    /**
     * Dinamicly call array functions using the object
     * 
     * @param string $func
     * @param mixed $arguments
     * @return mixed
     */
    public function __call(string $func, $arguments)
    {
        if (!\is_callable($func) || !\str_contains($func, 'array_')) {

            throw new \BadFunctionCallException(
                sprintf('Undefined array function "%s"', $func)
            );
        }

        return \call_user_func(
            $func,
            array_merge($this->all(), ...$arguments)
        );
    }

    /**
     * Append multiple values to the current array
     * 
     * @param array $values
     * @return self
     */
    public function multiAppend(array $values): self
    {
        foreach ($values as $key => $value) {

            $this->offsetSet(
                \is_int($key) ? $this->count() : $key . rand(0, 100),
                $value
            );
        }

        return $this;
    }

    /**
     * Get all the parameters
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->getArrayCopy();
    }
}
