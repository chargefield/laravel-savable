<?php

namespace Chargefield\Supermodels\Tests\Fixtures;

use Chargefield\Supermodels\Fields\Field;

class TestField extends Field
{
    public function handle(array $data = [])
    {
        return $this->value;
    }
}
