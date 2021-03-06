<?php

namespace Chargefield\Savable\Fields;

class IntegerField extends Field
{
    /**
     * @var bool
     */
    protected bool $strict = false;

    /**
     * @return $this
     */
    public function strict(): self
    {
        $this->strict = true;

        return $this;
    }

    /**
     * @param array $data
     * @return int|null
     */
    public function handle(array $data = [])
    {
        if (empty($this->value) && $this->nullable) {
            return null;
        }

        if (! is_numeric($this->value) && $this->strict) {
            if ($this->nullable) {
                return null;
            }

            return 0;
        }

        return intval($this->value);
    }
}
