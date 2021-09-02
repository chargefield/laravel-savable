<?php

namespace Chargefield\Supermodel\Tests\Fixtures;

use Chargefield\Supermodel\Fields\Field;

class TestField extends Field
{
    public function handle(array $fields = [])
    {
        return parent::handle($fields);
    }
}