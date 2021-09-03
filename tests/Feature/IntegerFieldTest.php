<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Fields\IntegerField;
use Chargefield\Supermodel\Tests\TestCase;

class IntegerFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(IntegerField::class, IntegerField::make('age'));
    }

    /** @test */
    public function it_can_set_and_get_an_integer_value()
    {
        $field = IntegerField::make('age');
        $this->assertInstanceOf(Field::class, $field->value(99));
        $this->assertEquals(99, $field->handle());
    }

    /** @test */
    public function it_can_set_and_get_an_integer_value_with_string_integer_value()
    {
        $field = IntegerField::make('age');
        $this->assertInstanceOf(Field::class, $field->value('99'));
        $this->assertEquals(99, $field->handle());
    }

    /** @test */
    public function it_can_set_and_get_an_integer_value_with_float_value()
    {
        $field = IntegerField::make('age');
        $this->assertInstanceOf(Field::class, $field->value(99.5));
        $this->assertEquals(99, $field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_not_set()
    {
        $field = IntegerField::make('age');
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_1_when_value_is_true()
    {
        $field = IntegerField::make('age')->value(true);
        $this->assertEquals(1, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_true_and_allow_null()
    {
        $field = IntegerField::make('age')->value(true)->strict()->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set_and_allow_null()
    {
        $field = IntegerField::make('age')->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_a_string()
    {
        $field = IntegerField::make('age')->value('not-an_integer');
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_a_string_and_strict_is_set()
    {
        $field = IntegerField::make('age')->value('not-an_integer')->strict();
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_a_string_and_allow_null()
    {
        $field = IntegerField::make('age')->value('not-an_integer')->strict()->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_null_and_set_to_nullable()
    {
        $field = IntegerField::make('age')->value(null)->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_null()
    {
        $field = IntegerField::make('age')->value(null);
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_empty_and_set_to_nullable()
    {
        $field = IntegerField::make('age')->value('')->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_empty()
    {
        $field = IntegerField::make('age')->value('');
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_0()
    {
        $field = IntegerField::make('age')->value(0)->strict()->nullable();
        $this->assertEquals(0, $field->handle());
    }
}
