<?php

namespace Chargefield\Savable\Tests\Fixtures;

use Chargefield\Savable\Fields\Field;

class TestField extends Field
{
    public function handle(array $data = [])
    {
        return $this->value;
    }
}
