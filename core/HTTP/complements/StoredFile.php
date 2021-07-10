<?php

namespace Core\Http\Complements;

use Core\Http\Response;

/**
 * Represents an stored file
 */
class StoredFile
{

    /**
     * The current file name
     */
    private string $filename;

    /**
     * The path of the current file
     */
    private string $path;

    /**
     * Sets the file data
     * @param string $path
     * @param string $filename
     * @return void
     */
    public function __construct(string $path, string $filename)
    {
        $this->path = $path;
        $this->filename = $filename;
    }

    /**
     * Returns the content of the current file
     * @return string
     */
    public function __toString(): string
    {
        return file_get_contents("$this->path/$this->filename");
    }

    /**
     * Returns the mimetype of the current file
     * @return string
     */
    public function type(): string
    {
        return mime_content_type("$this->path/$this->filename");
    }

    /**
     * Returns the size of the current file
     * @return int
     */
    public function size(): int
    {
        return filesize("$this->path/$this->filename");
    }

    /**
     * Renames the current file
     * @param string $newname the new name
     * @return void
     */
    public function rename(string $newname): void
    {
        rename("$this->path/$this->filename", "$this->path/$newname");
    }

    /**
     * Makes a copy of the current file
     * @param string $filename the new file name
     * @return void
     */
    public function copy(string $filename): void
    {
        copy("$this->path/$this->filename", "$this->path/$filename");
    }

    /**
     * Returns a file download response
     * @return Core\Http\Response
     */
    public function download(): Response
    {
        return new Response(
            $this,
            200,
            [
                'Pragma' => 'public',
                'Exipres' => 0,
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Disposition' => 'attachment; filename="' . basename($this->filename) . '";',
                'Content-Transfer-Encoding' => 'binary',
            ]
        );
    }
}
