<?php

namespace Chargefield\Savable\Exceptions;

use Exception;
use Illuminate\Http\UploadedFile;

class InvalidImageFileException extends Exception
{
    public function __construct($file)
    {
        $class = UploadedFile::class;
        $message = "{$file} needs to be an instance of {$class}.";
        parent::__construct($message);
    }
}
