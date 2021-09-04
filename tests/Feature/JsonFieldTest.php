<?php

namespace Chargefield\Supermodels\Tests\Feature;

use Chargefield\Supermodels\Fields\JsonField;
use Chargefield\Supermodels\Tests\TestCase;

class JsonFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(JsonField::class, JsonField::make('options'));
    }

    /** @test */
    public function it_returns_a_valid_json_string_when_valid_value_is_set()
    {
        $value = [
            'title' => 'Example Text',
            'body' => 'An example body.',
        ];
        $field = JsonField::make('options');
        $field->value($value);
        $this->assertEquals('{"title":"Example Text","body":"An example body."}', $field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_not_set()
    {
        $field = JsonField::make('options');
        $field->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_a_valid_formatted_json_when_pretty_and_valid_value_is_set()
    {
        $data = ['title' => 'Example Text', 'body' => 'An example body.'];
        $field = JsonField::make('options');
        $field->pretty();
        $field->value($data);
        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $field->handle());
    }

    /** @test */
    public function it_returns_false_when_depth_set_to_1_and_value_is_set_to_a_nested_array()
    {
        $data = ['options' => ['one', 'two']];
        $field = JsonField::make('options');
        $field->depth(1);
        $field->value($data);
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_depth_is_1_and_value_is_a_nested_array()
    {
        $data = ['options' => ['one', 'two']];
        $field = JsonField::make('options');
        $field->nullable();
        $field->depth(1);
        $field->value($data);
        $this->assertNull($field->handle());
    }
}
