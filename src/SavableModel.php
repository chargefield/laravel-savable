<?php

namespace Chargefield\Supermodel;

use Chargefield\Supermodel\Contracts\SavableInterface;
use Chargefield\Supermodel\Fields\Field;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

class SavableModel
{
    /**
     * @var SavableInterface
     */
    protected $model;

    /**
     * @var array|null
     */
    protected ?array $rules = null;

    /**
     * @var Validator
     */
    protected Validator $validator;

    /**
     * @var array
     */
    protected array $payload = [];

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setPayload(array $data): self
    {
        $this->payload = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param bool $throwsException
     * @return $this
     *
     * @throws ValidationException
     */
    public function validate(bool $throwsException = true): self
    {
        $this->validator = ValidatorFacade::make($this->payload, $this->getValidationRules());

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
     * @return array
     */
    public function getValidationRules(): array
    {
        if (is_null($this->rules)) {
            $this->rules = collect($this->model->savableColumns())
                ->whereInstanceOf(Field::class)
                ->mapWithKeys(function (Field $field) {
                    return [$field->getDataKey() => $field->getRules()];
                })
                ->filter()
                ->toArray();
        }

        return $this->rules;
    }

    public function saveData()
    {
        return $this->model->saveData();
    }
}