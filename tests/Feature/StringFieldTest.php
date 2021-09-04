<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\StringField;
use Chargefield\Supermodel\Tests\TestCase;

class StringFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(StringField::class, StringField::make('title'));
    }

    /** @test */
    public function it_returns_a_string_value_when_a_valid_value_is_set()
    {
        $value = 'Example Text';
        $field = StringField::make('title');
        $field->value($value);
        $this->assertEquals($value, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        $field = StringField::make('title');
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_an_empty_string_when_value_is_set_to_an_empty_string()
    {
        $field = StringField::make('title');
        $field->value('');
        $this->assertEmpty($field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_set_to_an_empty_string()
    {
        $field = StringField::make('title');
        $field->nullable();
        $field->value('');
        $this->assertNull($field->handle());
    }
}
