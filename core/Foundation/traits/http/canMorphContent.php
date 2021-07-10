<?php

namespace Core\Foundation\Traits\Http;

trait canMorphContent
{

    protected function canSetContent($content): bool
    {
        return !is_null($content);
    }

    protected function shouldBeJson($content): bool
    {
        return is_array($content);
    }

    protected function morphToJson(array $content): string|false
    {
        return json_encode($content);
    }
}
