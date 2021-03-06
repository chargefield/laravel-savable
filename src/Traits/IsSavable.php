<?php

namespace Chargefield\Savable\Traits;

use Chargefield\Savable\Exceptions\NotSavableException;
use Chargefield\Savable\Fields\Field;
use Chargefield\Savable\Savable;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * @method static findOrFail($model)
 */
trait IsSavable
{
    /**
     * @return Field[]
     */
    public function savableColumns(): array
    {
        return [];
    }

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
     * @return Savable
     *
     * @throws NotSavableException
     */
    public function savable(array $data = []): Savable
    {
        /** @var Model $this */
        return new Savable($this, $data);
    }
}
