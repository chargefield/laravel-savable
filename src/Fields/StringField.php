<?php

namespace Chargefield\Supermodel\Fields;

class StringField extends Field
{
    /**
     * @param array $fields
     * @return string|null
     */
    public function handle(array $fields = [])
    {
        if (empty($this->value) && $this->nullable) {
            return null;
        }

        return $this->value;
    }
}
