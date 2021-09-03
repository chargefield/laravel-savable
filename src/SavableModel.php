<?php

namespace Chargefield\Supermodel;

use Chargefield\Supermodel\Exceptions\FieldNotFoundException;
use Chargefield\Supermodel\Exceptions\NoColumnsToSaveException;
use Chargefield\Supermodel\Exceptions\NotSavableException;
use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Traits\Savable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Throwable;

class SavableModel
{
    protected Model $model;

    /**
     * @var array|null
     */
    protected ?array $rules = null;

    /**
     * @var array|null
     */
    protected ?array $columns = null;

    /**
     * @var Validator|null
     */
    protected ?Validator $validator = null;

    /**
     * @var array
     */
    protected array $data = [];

    public function __construct(Model $model, array $data = [])
    {
        if (! $this->isSavable($model)) {
            throw new NotSavableException($model);
        }

        $this->model = $model;
        $this->set($data);
    }

    /**
     * @param Model $model
     * @return bool
     */
    protected function isSavable(Model $model): bool
    {
        $traits = class_uses_recursive($model);

        return in_array(Savable::class, $traits);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function set(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param Field[] $columns
     * @return $this
     */
    public function columns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param bool $throwsException
     * @return $this
     *
     * @throws ValidationException
     */
    public function validate(bool $throwsException = true): self
    {
        $this->validator = ValidatorFacade::make($this->data, $this->getValidationRules());

        if ($throwsException) {
            $this->validator->validate();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasValidationErrors(): bool
    {
        if ($this->validator instanceof Validator) {
            return $this->validator->fails();
        }

        return false;
    }

    /**
     * @return MessageBag
     */
    public function getValidationErrors(): MessageBag
    {
        if ($this->validator instanceof Validator) {
            return $this->validator->errors();
        }

        return new MessageBag();
    }

    /**
     * @return Field[]
     */
    protected function getColumns(): array
    {
        return $this->columns ?? $this->model->savableColumns();
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        if (is_null($this->rules)) {
            $this->rules = collect($this->getColumns())
                ->whereInstanceOf(Field::class)
                ->mapWithKeys(function (Field $field) {
                    return [$field->getDataKey() => $field->getRules()];
                })
                ->filter()
                ->toArray();
        }

        return $this->rules;
    }

    /**
     * @return Model
     *
     * @throws FieldNotFoundException
     * @throws NoColumnsToSaveException
     * @throws Throwable
     */
    public function save(): Model
    {
        $columns = $this->getColumns();

        if (empty($columns)) {
            throw new NoColumnsToSaveException($this->model);
        }

        /**
         * @var string $columnName
         * @var Field $column
         */
        foreach ($columns as $column) {
            if (! ($column instanceof Field)) {
                throw new FieldNotFoundException($this->model, $column);
            }

            $dataKey = $column->getDataKey();

            if (isset($this->data[$dataKey])) {
                $column->setValue($this->data[$dataKey]);
            }

            $columnName = $column->getColumnName();

            $this->model->{$columnName} = $column->handle($this->data);
        }

        $this->model->saveOrFail();

        return $this->model;
    }
}
