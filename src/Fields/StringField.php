<?php

namespace Chargefield\Supermodel\Fields;

class StringField extends Field
{
    /**
     * @param array $data
     * @return string|null
     */
    public function handle(array $data = [])
    {
        if (empty($this->value) && $this->nullable) {
            return null;
        }

        return $this->value;
    }
}
