<?php

namespace Core\Support\Files;

use Core\FileSystems\Storage;
use Core\Http\RequestComplements\UploadedFile;

trait HandlesImages
{
    private array $image_types = [
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_SWF,
        IMAGETYPE_PSD,
        IMAGETYPE_BMP,
        IMAGETYPE_TIFF_II,
        IMAGETYPE_TIFF_MM,
        IMAGETYPE_JPC,
        IMAGETYPE_JP2,
        IMAGETYPE_JPX,
        IMAGETYPE_JB2,
        IMAGETYPE_SWC,
        IMAGETYPE_IFF,
        IMAGETYPE_WBMP,
        IMAGETYPE_XBM,
        IMAGETYPE_ICO,
        IMAGETYPE_WEBP
    ];

    private array $image_mime_type = [];

    public function isImage(UploadedFile $file): bool
    {
        $this->buildImageMimeTypes();
        return in_array($file->currentFile()->type, $this->image_mime_type);
    }

    public function upload(UploadedFile $file, string $to, string $path): void
    {
        Storage::in($to)->put(
            $file,
            $path
        );
    }

    public function uploadIfIsImage(UploadedFile $key, string $to, string $path): void
    {
        if ($this->isImage($key)) $this->upload($key, $to, $path);
    }

    public function deleteFile(string $path, string $filename): void
    {
        Storage::in($path)->delete($filename);
    }

    public function getFile($path, string $filename): string
    {
        return Storage::in($path)->get($filename);
    }

    private function buildImageMimeTypes(): void
    {
        array_map(fn ($value) => $this->image_mime_type[] = image_type_to_mime_type($value), $this->image_types);
    }
}
