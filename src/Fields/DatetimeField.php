<?php

namespace Chargefield\Supermodels\Fields;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;

class DatetimeField extends Field
{
    /**
     * @param array $data
     * @return \Illuminate\Support\Carbon|null
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function handle(array $data = [])
    {
        if (empty($this->value)) {
            return null;
        }

        try {
            return Carbon::parse($this->value);
        } catch (InvalidFormatException $e) {
            if ($this->nullable) {
                return null;
            }

            throw $e;
        }
    }
}
