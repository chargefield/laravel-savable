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
    protected string $fieldName;

    /**
     * @var mixed
     */
    protected $rules;

    public function __construct(string $column, $defaultValue = null)
    {
        $this->column = $column;
        $this->value($defaultValue);
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
    public function value($value): self
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
    public function getFieldName(): string
    {
        return $this->fieldName ?? $this->getColumnName();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function fieldName(string $name): self
    {
        $this->fieldName = $name;

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
    public function transform(Closure $callback): self
    {
        $this->computeCallback = $callback;

        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function compute(array $data = [])
    {
        if ($this->computeCallback instanceof Closure) {
            return ($this->computeCallback)($this->getFieldName(), $this->handle($data), $data);
        }

        return $this->handle($data);
    }

    /**
     * @param $rules
     * @return $this
     */
    public function rules($rules): self
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
        return ! empty($this->rules);
    }

    /**
     * @param array $fields
     * @return mixed
     */
    public function handle(array $fields = [])
    {
        return $this->value;
    }
}
