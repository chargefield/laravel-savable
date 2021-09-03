<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Fields\JsonField;
use Chargefield\Supermodel\Tests\TestCase;

class JsonFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(JsonField::class, JsonField::make('options'));
    }

    /** @test */
    public function it_can_set_and_get_the_value()
    {
        $value = [
            'title' => 'Example Text',
            'body' => 'An example body.',
        ];
        $field = JsonField::make('options');
        $this->assertInstanceOf(Field::class, $field->value($value));
        $this->assertEquals('{"title":"Example Text","body":"An example body."}', $field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        $field = JsonField::make('options')->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_can_set_pretty_json()
    {
        $data = ['title' => 'Example Text', 'body' => 'An example body.'];
        $field = JsonField::make('options')->value($data)->pretty();
        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $field->handle());
    }

    /** @test */
    public function it_can_set_depth_to_1_and_return_false()
    {
        $data = ['options' => ['one', 'two']];
        $field = JsonField::make('options')->value($data)->depth(1);
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_can_set_depth_to_1_and_return_null()
    {
        $data = ['options' => ['one', 'two']];
        $field = JsonField::make('options')->value($data)->depth(1)->nullable();
        $this->assertNull($field->handle());
    }
}
