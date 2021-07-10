<?php

namespace Core\Client;

use Core\Http\Server;

class ViewHelper
{
    public function url(string $path): string
    {
        return Server::host() . Server::uri() . $path;
    }
}
