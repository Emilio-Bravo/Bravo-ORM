<?php

namespace Core\Support\Files;

use Core\Http\Files;
use Core\Http\RequestComplements\UploadedFile;

trait HandlesRequestFiles
{
    public function hasFiles(): bool
    {
        foreach (Files::all() as $key) {
            $file = new UploadedFile($key);
            return $file->hasContents();
        }
    }

    public function hasFile($key): bool
    {
        $file = new UploadedFile($key);
        return $file->hasContents();
    }

    public function file($key)
    {
        return new UploadedFile($key);
    }

    public function getAllUploadedFiles()
    {
        return Files::all();
    }
}
