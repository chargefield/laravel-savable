<?php

namespace Chargefield\Supermodel\Fields;

class JsonField extends Field
{
    /**
     * @param array $fields
     * @return string
     */
    public function handle(array $fields = [])
    {
        $value = parent::handle($fields);

        if (! is_array($value) && $this->nullable) {
            return null;
        }

        return json_encode($value);
    }
}