<?php

namespace Core\Http\RequestComplements;

use Core\Http\Files;


class UploadedFile
{

    /**
     * The current file to work with
     */
    private object|bool $currentFile;

    /**
     * Dangeorus considered chars that will 
     * be replaced for _ in the filename
     */
    private array $dangerousFilenameChars = [
        ' ', '"', "'",
        '&', '/', '\\',
        '?', '¿', '#',
        '<', '>', '$',
        '+', '%', '!',
        '`', '*', '|',
        '=', ':', '@',
        '{', '}', ';',
        ',', '[', ']'
    ];

    /**
     * Sets the file to work with
     * @return object
     */
    public function __construct(string $key)
    {
        $this->setCurrentFile($key);
        return $this;
    }

    /**
     * Retruns the temoral name of the current file
     * @return string
     */
    public function tmpName(): string
    {
        return $this->currentFile->tmp_name;
    }

    /**
     * Returns the name of the current file, will replace dangerous chars from the filename
     * @return string
     */
    public function name(): string
    {
        return str_replace($this->dangerousFilenameChars, '_', $this->currentFile->name);
    }

    /**
     * Returns the contents of a file
     * @return string
     */
    public function getContents(): string
    {
        return file_get_contents($this->tmpName());
    }

    /**
     * Returns the size of the current file
     * @return string
     */
    public function size(): string
    {
        return $this->currentFile->size;
    }

    /**
     * Returns the type of the current file
     * @return string
     */
    public function type(): string
    {
        return $this->currentFile->type;
    }

    /**
     * Returns the current file
     * @return object
     */
    public function currentFile(): object
    {
        return $this->currentFile;
    }

    /**
     * Determines wheter a file has contents or not
     * @return bool
     */
    public function hasContents(): bool
    {
        return !is_bool($this->currentFile) && $this->currentFile->type != null;
    }

    /**
     * Sets the current file to work with
     * @param string $key
     * @return object|false Returns false if the requested key doesnt exist
     */
    private function setCurrentFile(string $key): object|false
    {
        if (property_exists(Files::all(), $key)) {
            return $this->currentFile = Files::get($key);
        }
        return $this->currentFile = false;
    }
}

