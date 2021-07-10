<?php

namespace Core\Foundation\Traits\Http;

trait Renderable
{
    public function render($content)
    {
        echo $content;
    }
}