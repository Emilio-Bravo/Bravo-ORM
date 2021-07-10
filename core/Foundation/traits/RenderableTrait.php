<?php

namespace Core\Http\Traits;

trait Renderable
{
    public function render($content)
    {
        echo $content;
    }
}