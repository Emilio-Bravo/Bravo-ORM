<?php

namespace Core\Config\Support;

trait interactsWithAuthConfig
{
    
    use interactsWithPathSettings;

    /**
     * Returns the validator config
     * @return object
     */
    public function getAuthConfig()
    {
        return (object) require $this->getPathKey('auth_config');
    }
}
