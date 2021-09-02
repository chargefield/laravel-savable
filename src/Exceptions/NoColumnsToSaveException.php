<?php

namespace Chargefield\Supermodel\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class NoColumnsToSaveException extends Exception
{
    public function __construct()
    {
        $message = "The savableColumns methods is empty.";
        parent::__construct($message);
    }
}