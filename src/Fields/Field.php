<?php

namespace Chargefield\Supermodels\Fields;

use BadMethodCallException;
use Closure;

/**
 * @method void assertHandle($value, array $data = [])
 * @method void assertTransform($value, array $data = [])
 * @method void assertValidation($value)
 */
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
     * @var FieldTesting|null
     */
    protected ?FieldTesting $test = null;

    /**
     * @var mixed
     */
    protected $rules;

    public function __construct(string $column, $defaultValue = null, bool $test = false)
    {
        if ($test) {
            $this->test = new FieldTesting($this);
        }

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
     * @param string $column
     * @param null $defaultValue
     * @return static
     */
    public static function fake(string $column, $defaultValue = null): self
    {
        return new static($column, $defaultValue, true);
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
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
     * @param array $data
     * @return mixed
     */
    public function handle(array $data = [])
    {
        return $this->value;
    }

    public function __call($name, $arguments)
    {
        if (! ($this->test instanceof FieldTesting)) {
            throw new BadMethodCallException;
        }

        if (! method_exists($this->test, $name)) {
            throw new BadMethodCallException;
        }

        $this->test->{$name}(...$arguments);
    }
}
