<?php

namespace Core\Client;

use Core\Config\Support\interactsWithViewDependencies;

class View
{

    use interactsWithViewDependencies;

    private array $files = [];

    public function render(string $file, array $vars = [])
    {
        $content = $this->transform("{$this->view_path}$file.php", $vars);
        return $this->buildLayout($content);
    }

    private function transform(string $path, array $vars = [])
    {
        ob_start();
        foreach ($vars as $var => $value) {
            $$var = $value;
        }
        foreach ($this->getViewDependencies() as $helper => $class) {
            $$helper = new $class;
        }
        require_once $path;
        return ob_get_clean();
    }

    private function buildLayout($file_content)
    {
        preg_match_all('/{{+[\w\d]+}}/', $file_content, $matches);

        foreach ($matches[0] as $match) {
            $sanitized = preg_replace('/[{}]/', '', $match);
            (array) $this->files[] = $this->transform("{$this->layout_path}$sanitized.php");
        }

        return str_ireplace($matches[0], $this->files, $file_content);
    }
}
