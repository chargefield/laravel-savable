<?php

namespace Chargefield\Supermodel\Traits;

use Chargefield\Supermodel\Exceptions\NotSavableException;
use Chargefield\Supermodel\SavableModel;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * @method static findOrFail($model)
 */
trait Savable
{
    /**
     * @return array
     */
    abstract public function savableColumns(): array;

    /**
     * @param mixed $model
     * @return Model|static
     *
     * @throws Throwable
     */
    public static function make($model = null): self
    {
        if ($model instanceof Model) {
            return $model;
        }

        if (is_null($model)) {
            return (new static);
        }

        return static::findOrFail($model);
    }

    /**
     * @param array $data
     * @return SavableModel
     *
     * @throws NotSavableException
     */
    public function savable(array $data = []): SavableModel
    {
        /** @var Model $this */
        return new SavableModel($this, $data);
    }
}