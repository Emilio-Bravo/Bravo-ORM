<?php

namespace Core\Http\ResponseComplements;

class HeaderBag implements \IteratorAggregate, \Countable
{
    private array $headers = [];
    private array $cache_control = [];

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public function set(string $name, string $value): void
    {
        $this->headers[$name] = $value;

        if ($name == 'cache-control') $this->cache_control[$name] = $value;
    }

    public function get(string $key)
    {
        if (in_array($key, $this->headers)) {
            return $this->headers[$key];
        }
    }

    public function remove(string $key): void
    {
        if (in_array($key, $this->headers)) {
            unset($this->headers[$key]);
        }
    }

    public function has(string $key): bool
    {
        return isset($this->headers[$key]);
    }

    public function count(): int
    {
        return \count($this->headers);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->headers);
    }

    public function hasCacheControlDirective(string $directive): bool
    {
        return \in_array($directive, $this->cache_control);
    }

    public function addCacheControlDirective(string $key): void
    {
        $this->cache_control[$key] = $key;
    }

    public function removeCacheControlDirective(string $key): void
    {
        if (\in_array($key, $this->cache_control)) {
            unset($this->cache_control[$key]);
        }
    }

    public function getCacheControlDirectives()
    {
        if (\in_array('Cache-Control', $this->headers)) {
            return array_intersect($this->headers, ['cache-control', 'Cache-Control']);
        }
    }
}
