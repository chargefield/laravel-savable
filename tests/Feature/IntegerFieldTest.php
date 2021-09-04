<?php

namespace Chargefield\Supermodel\Tests\Feature;

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
    public function it_returns_an_integer_when_a_valid_integer_value_is_set()
    {
        $field = IntegerField::make('age');
        $field->value(99);
        $this->assertEquals(99, $field->handle());
    }

    /** @test */
    public function it_returns_an_integer_when_a_valid_string_value_is_set()
    {
        $field = IntegerField::make('age');
        $field->value('99');
        $this->assertEquals(99, $field->handle());
    }

    /** @test */
    public function it_returns_an_integer_when_a_valid_float_value_is_set()
    {
        $field = IntegerField::make('age');
        $field->value(99.5);
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
    public function it_returns_null_when_nullable_and_value_is_true()
    {
        $field = IntegerField::make('age')->value(true)->strict()->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_not_set()
    {
        $field = IntegerField::make('age')->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_an_invalid_string()
    {
        $field = IntegerField::make('age')->value('not-an_integer');
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_0_when_strict_and_value_is_an_invalid_string()
    {
        $field = IntegerField::make('age');
        $field->strict();
        $field->value('not-an_integer');
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_strict_and_nullable_and_value_is_an_invalid_string()
    {
        $field = IntegerField::make('age');
        $field->nullable();
        $field->strict();
        $field->value('not-an_integer');
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_null()
    {
        $field = IntegerField::make('age');
        $field->nullable();
        $field->value(null);
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_null()
    {
        $field = IntegerField::make('age');
        $field->value(null);
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_an_empty_string()
    {
        $field = IntegerField::make('age');
        $field->nullable();
        $field->value('');
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_0_when_value_is_an_empty_string()
    {
        $field = IntegerField::make('age');
        $field->value('');
        $this->assertEquals(0, $field->handle());
    }

    /** @test */
    public function it_returns_0_when_nullable_and_strict_and_value_is_0()
    {
        $field = IntegerField::make('age');
        $field->nullable();
        $field->strict();
        $field->value(0);
        $this->assertEquals(0, $field->handle());
    }
}
