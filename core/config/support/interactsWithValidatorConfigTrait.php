<?php

namespace Core\Config\Support;

trait interactsWithValidatorConfig
{
    
    use interactsWithPathSettings;

    /**
     * Returns the validator config
     * @return object
     */
    public function getValidatorConfig()
    {
        return (object) require $this->getPathKey('validator_config');
    }
}
