<?php

namespace Chargefield\Supermodel\Fields;

use Illuminate\Support\Str;

class SlugField extends Field
{
    protected string $fromField;

    /**
     * @param string $field
     * @return $this
     */
    public function fromField(string $field): self
    {
        $this->fromField = $field;

        return $this;
    }

    /**
     * @param array $fields
     * @return mixed|string|null
     */
    public function handle(array $fields = [])
    {
        $value = parent::handle($fields);

        if (! empty($this->fromField) && isset($fields[$this->fromField])) {
            $value = $fields[$this->fromField];
        }

        if (! is_string($value)) {
            $value = null;
        }

        if (is_null($value) && $this->nullable) {
            return null;
        }

        return Str::slug($value);
    }
}