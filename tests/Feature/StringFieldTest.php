<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Fields\StringField;
use Chargefield\Supermodel\Tests\TestCase;

class StringFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_string_field_instance()
    {
        $this->assertInstanceOf(StringField::class, StringField::make('title'));
    }

    /** @test */
    public function it_can_set_and_get_the_value()
    {
        $value = 'Example Text';
        $field = StringField::make('title');
        $this->assertInstanceOf(Field::class, $field->setValue($value));
        $this->assertEquals($value, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        $field = StringField::make('title');
        $this->assertNull($field->handle());
    }
}