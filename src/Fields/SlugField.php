<?php

namespace Chargefield\Supermodel\Fields;

use Illuminate\Support\Str;

class SlugField extends Field
{
    /**
     * @var string
     */
    protected string $fromField;

    /**
     * @var string
     */
    protected string $separator = '-';

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
     * @param string $separator
     * @return $this
     */
    public function separateBy(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @param array $fields
     * @return string|null
     */
    public function handle(array $fields = [])
    {
        $value = $this->value;

        if (! empty($this->fromField) && isset($fields[$this->fromField])) {
            $value = $fields[$this->fromField];
        }

        if (! is_string($value)) {
            $value = null;
        }

        if (empty($value) && $this->nullable) {
            return null;
        }

        return Str::slug($value, $this->separator);
    }
}
