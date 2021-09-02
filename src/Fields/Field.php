<?php

namespace Chargefield\Supermodel\Fields;

use Closure;

abstract class Field
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected bool $nullable = false;

    /**
     * @var Closure|null
     */
    protected ?Closure $computeCallback = null;

    /**
     * @var string
     */
    protected string $column;

    /**
     * @var string
     */
    protected string $dataKey;

    /**
     * @var mixed
     */
    protected $rules;

    public function __construct(string $column, $defaultValue = null)
    {
        $this->column = $column;
        $this->setValue($defaultValue);
    }

    /**
     * @param string $column
     * @param null $defaultValue
     * @return static
     */
    public static function make(string $column, $defaultValue = null): self
    {
        return new static($column, $defaultValue);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getDataKey(): string
    {
        return $this->dataKey ?? $this->getColumnName();
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setDataKey(string $key): self
    {
        $this->dataKey = $key;

        return $this;
    }

    /**
     * @return $this
     */
    public function nullable(): self
    {
        $this->nullable = true;

        return $this;
    }

    /**
     * @param Closure $callback
     * @return $this
     */
    public function compute(Closure $callback): self
    {
        $this->computeCallback = $callback;

        return $this;
    }

    /**
     * @param array $args
     * @return $this
     */
    public function withComputed(array $args = []): self
    {
        if ($this->computeCallback instanceof Closure) {
            $this->setValue(($this->computeCallback)($this->getDataKey(), $this->value, ...$args));
        }

        return $this;
    }

    /**
     * @param $rules
     * @return $this
     */
    public function setRules($rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return bool
     */
    public function hasRules(): bool
    {
        return !empty($this->rules);
    }

    /**
     * @param array $fields
     * @return mixed
     */
    public function handle(array $fields = [])
    {
        if (is_null($this->value) && $this->nullable) {
            return null;
        }

        return $this->value;
    }
}