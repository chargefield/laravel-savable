<?php

namespace Chargefield\Savable\Tests\Feature;

use Chargefield\Savable\Fields\BooleanField;
use Chargefield\Savable\Tests\TestCase;

class BooleanFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(BooleanField::class, BooleanField::make('is_testing'));
    }

    /** @test */
    public function it_can_set_a_value_true_and_return_true()
    {
        $field = BooleanField::make('is_testing');
        $field->value(true);
        $this->assertTrue($field->handle());
    }

    /** @test */
    public function it_can_set_a_string_value_true_and_return_true()
    {
        $field = BooleanField::make('is_testing');
        $field->value('true');
        $this->assertTrue($field->handle());
    }

    /** @test */
    public function it_can_set_a_string_value_1_and_return_true()
    {
        $field = BooleanField::make('is_testing');
        $field->value('1');
        $this->assertTrue($field->handle());
    }

    /** @test */
    public function it_can_set_a_value_false_and_return_false()
    {
        $field = BooleanField::make('is_testing');
        $field->value(false);
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_can_set_a_string_value_false_and_return_false()
    {
        $field = BooleanField::make('is_testing');
        $field->value('false');
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_can_set_a_string_value_0_and_return_false()
    {
        $field = BooleanField::make('is_testing');
        $field->value('0');
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_returns_false_when_a_value_is_not_set()
    {
        $field = BooleanField::make('is_testing');
        $this->assertFalse($field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_not_a_boolean()
    {
        $field = BooleanField::make('is_testing');
        $field->nullable();
        $field->value('not-a-boolean');
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_null()
    {
        $field = BooleanField::make('is_testing');
        $field->nullable();
        $field->value(null);
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_empty_string()
    {
        $field = BooleanField::make('is_testing');
        $field->nullable();
        $field->value('');
        $this->assertNull($field->handle());
    }
}
