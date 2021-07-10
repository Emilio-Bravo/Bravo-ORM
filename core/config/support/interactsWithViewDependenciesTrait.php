<?php

namespace Core\Config\Support;

use Core\Config\Support\interactsWithPathSettings;

trait interactsWithViewDependencies
{

    use interactsWithPathSettings;

    /**
     * Returns the view dependencies
     * @return array
     */
    public function getViewDependencies(): array
    {
        return (array) require $this->getPathKey('view_dependencies');
    }
}
