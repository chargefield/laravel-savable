<?php

namespace Chargefield\Supermodel\Exceptions;

use Exception;
use Chargefield\Supermodel\Fields\Field;
use Illuminate\Database\Eloquent\Model;

class FieldNotFoundException extends Exception
{
    public function __construct($column)
    {
        $field = Field::class;
        $message = "{$column} is not a valid {$field}.";
        parent::__construct($message);
    }
}