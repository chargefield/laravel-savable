<?php

namespace Chargefield\Supermodel\Fields;

class BooleanField extends Field
{
    /**
     * @param array $fields
     * @return bool|null
     */
    public function handle(array $fields = [])
    {
        return filter_var(
            parent::handle($fields),
            FILTER_VALIDATE_BOOLEAN,
            $this->nullable ? FILTER_NULL_ON_FAILURE : null
        );
    }
}