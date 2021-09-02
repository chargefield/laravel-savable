<?php

namespace Chargefield\Supermodel\Traits;

use Chargefield\Supermodel\Contracts\SavableInterface;
use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Exceptions\FieldNotFoundException;
use Chargefield\Supermodel\Exceptions\NoColumnsToSaveException;
use Chargefield\Supermodel\SavableModel;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * @method static findOrFail($model)
 */
trait Savable
{
    /**
     * @var SavableModel|null
     */
    protected ?SavableModel $savable = null;

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    abstract public function save(array $options = []);

    /**
     * Save the model to the database within a transaction.
     *
     * @param  array  $options
     * @return bool
     *
     * @throws Throwable
     */
    abstract public function saveOrFail(array $options = []);

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    abstract public function getTable();

    /**
     * Get the database connection for the model.
     *
     * @return Connection
     */
    abstract public function getConnection();

    /**
     * @return array
     */
    abstract public function savableColumns(): array;

    /**
     * @param mixed $model
     * @return Model|SavableInterface|static
     *
     * @throws Throwable
     */
    public static function make($model = null): self
    {
        if ($model instanceof SavableInterface) {
            return $model->makeSavable();
        }

        if (is_null($model)) {
            return (new static)->makeSavable();
        }

        return static::findOrFail($model)->makeSavable();
    }

    /**
     * @return $this
     */
    public function makeSavable()
    {
        $this->savable = new SavableModel($this);

        return $this;
    }

    /**
     * @return SavableModel
     */
    public function getSavable(): SavableModel
    {
        if (is_null($this->savable)) {
            $this->makeSavable();
        }

        return $this->savable;
    }

    /**
     * @param array $data
     * @return SavableModel
     */
    public function setPayload(array $data): SavableModel
    {
        return $this->getSavable()->setPayload($data);
    }

    /**
     * @return bool
     */
    public function hasValidationErrors(): bool
    {
        return $this->getSavable()->hasValidationErrors();
    }

    /**
     * @return MessageBag
     */
    public function getValidationErrors(): MessageBag
    {
        return $this->getSavable()->getValidationErrors();
    }

    /**
     * @return $this
     *
     * @throws FieldNotFoundException
     * @throws NoColumnsToSaveException
     * @throws Throwable
     */
    public function saveData()
    {
        $data = $this->getSavable()->getPayload();
        $tableColumns = $this->getTableColumns();
        $columns = $this->savableColumns();

        if (empty($columns)) {
            throw new NoColumnsToSaveException;
        }

        /**
         * @var string $columnName
         * @var Field $column
         */
        foreach ($columns as $column) {
            if (! ($column instanceof Field)) {
                throw new FieldNotFoundException($column);
            }

            $dataKey = $column->getDataKey();

            if (isset($data[$dataKey])) {
                $column->setValue($data[$dataKey]);
            }

            $columnName = $column->getColumnName();

            if (isset($columnName, $tableColumns)) {
                $this->{$columnName} = $column->handle($data);
            }
        }

        $this->saveOrFail();

        return $this;
    }

    /**
     * @return array
     */
    protected function getTableColumns(): array
    {
        /** @var Model $this */
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}