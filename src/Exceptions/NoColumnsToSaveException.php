<?php

namespace Chargefield\Supermodel\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class NoColumnsToSaveException extends Exception
{
    public function __construct(Model $model)
    {
        $class = get_class($model);
        $message = "{$class}::savableColumns() is empty.";
        parent::__construct($message);
    }
}
