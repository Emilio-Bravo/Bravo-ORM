<?php

namespace Core\Config\Support;

trait interactsWithPathSettings
{

    /**
     * Will try to find the specified property in the paths array
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getPathKey($key);
    }

    /**
     * If the key exists will return the content of it
     * @param string $key
     * @return mixed
     */
    public function getPathKey(string $key)
    {
        if (property_exists($this->getPathSettings(), $key)) {
            return $this->getPathSettings()->$key;
        }
    }

    /**
     * Returns an object of all the array elements of the paths file
     * @return object
     */
    public function getPathSettings()
    {
        return (object) require __DIR__ . '/../../config/appPaths.php';
    }
}
