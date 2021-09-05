<?php

namespace Chargefield\Savable\Fields;

use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\Assert;

class FieldTesting
{
    /**
     * @var Field
     */
    protected Field $field;

    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * @param $value
     * @param array $data
     */
    public function assertHandle($value, array $data = []): void
    {
        Assert::assertEquals($this->field->handle($data), $value);
    }

    /**
     * @param $value
     * @param array $data
     */
    public function assertTransform($value, array $data = []): void
    {
        Assert::assertEquals($this->field->compute($data), $value);
    }

    /**
     * @param $value
     */
    public function assertValidation($value): void
    {
        $fieldName = $this->field->getFieldName();
        $validator = Validator::make([$fieldName => $value], [$fieldName => $this->field->getRules()]);
        Assert::assertTrue(! $validator->fails(), "Failed asserting that [{$fieldName} => '{$value}'] passes validation.");
    }
}
