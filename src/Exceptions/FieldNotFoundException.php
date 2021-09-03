<?php

namespace Chargefield\Supermodel\Exceptions;

use Chargefield\Supermodel\Fields\Field;
use Exception;
use Illuminate\Database\Eloquent\Model;

class FieldNotFoundException extends Exception
{
    public function __construct(Model $model, $column)
    {
        $class = get_class($model);
        $field = Field::class;
        $message = "{$column} is not a valid {$field} on {$class}.";
        parent::__construct($message);
    }
}
