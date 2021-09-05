<?php

namespace Chargefield\Savable\Fields;

class JsonField extends Field
{
    /**
     * @var bool
     */
    protected bool $pretty = false;

    /**
     * @var int
     */
    protected int $depth = 512;

    /**
     * @return $this
     */
    public function pretty(): self
    {
        $this->pretty = true;

        return $this;
    }

    /**
     * @param int $depth
     * @return $this
     */
    public function depth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * @param array $data
     * @return string|false|null
     */
    public function handle(array $data = [])
    {
        if (! is_array($this->value) && $this->nullable) {
            return null;
        }

        $json = json_encode($this->value, $this->getFlag(), $this->depth);

        if (! $json && $this->nullable) {
            return null;
        }

        return $json;
    }

    protected function getFlag(): int
    {
        return $this->pretty ? JSON_PRETTY_PRINT : 0;
    }
}
