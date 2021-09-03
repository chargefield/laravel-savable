<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\BooleanField;
use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Tests\TestCase;

class BooleanFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_string_field_instance()
    {
        $this->assertInstanceOf(BooleanField::class, BooleanField::make('is_testing'));
    }

    /** @test */
    public function it_can_set_and_get_true_from_the_boolean_value()
    {
        $field = BooleanField::make('is_testing');
        $this->assertInstanceOf(Field::class, $field->setValue(true));
        $this->assertTrue($field->handle());
    }

    /** @test */
    public function it_can_set_and_get_true_from_the_string_boolean_value()
    {
        $field = BooleanField::make('is_testing');
        $this->assertInstanceOf(Field::class, $field->setValue('true'));
        $this->assertTrue($field->handle());
    }

    /** @test */
    public function it_can_set_and_get_true_from_the_integer_boolean_value()
    {
        $field = BooleanField::make('is_testing');
        $this->assertInstanceOf(Field::class, $field->setValue('1'));
        $this->assertTrue($field->handle());
    }

    /** @test */
    public function it_can_set_and_get_false_from_the_boolean_value()
    {
        $field = BooleanField::make('is_testing');
        $this->assertInstanceOf(Field::class, $field->setValue(false));
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_can_set_and_get_false_from_the_string_boolean_value()
    {
        $field = BooleanField::make('is_testing');
        $this->assertInstanceOf(Field::class, $field->setValue('false'));
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_can_set_and_get_false_from_the_integer_boolean_value()
    {
        $field = BooleanField::make('is_testing');
        $this->assertInstanceOf(Field::class, $field->setValue('0'));
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_returns_false_when_value_is_not_set()
    {
        $field = BooleanField::make('is_testing');
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set_and_allow_null()
    {
        $field = BooleanField::make('is_testing')->setValue('not-a-boolean')->nullable();
        $this->assertNull($field->handle());
    }
}