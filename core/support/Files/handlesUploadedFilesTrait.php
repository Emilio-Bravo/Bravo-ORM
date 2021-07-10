<?php

namespace Core\Support\Files;

use Core\Http\RequestComplements\UploadedFile;
use Core\FileSystems\Storage;
use Core\Support\Crypto;

trait handlesUploadedFiles
{
    public function uploadFile(string $requestKey, string $folder): void
    {
        if ($this->hasFile($requestKey)) {

            $filePath = Crypto::encStamp(
                $this->file($requestKey)->name()
            );

            Storage::in($folder)->put(
                new UploadedFile($requestKey),
                $filePath
            );

            $this->setInputValue($requestKey, $filePath);
        }
    }
}
