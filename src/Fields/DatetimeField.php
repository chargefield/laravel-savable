<?php

namespace Chargefield\Supermodel\Fields;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;

class DatetimeField extends Field
{
    /**
     * @param array $fields
     * @return \Illuminate\Support\Carbon|null
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function handle(array $fields = [])
    {
        $value = parent::handle($fields);

        if (is_null($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (InvalidFormatException $e) {
            if ($this->nullable) {
                return null;
            }

            throw $e;
        }
    }
}
