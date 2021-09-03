<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Carbon\Exceptions\InvalidFormatException;
use Chargefield\Supermodel\Fields\DatetimeField;
use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Tests\TestCase;
use Illuminate\Support\Carbon;

class DatetimeFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_string_field_instance()
    {
        $this->assertInstanceOf(DatetimeField::class, DatetimeField::make('created_at'));
    }

    /** @test */
    public function it_can_set_and_get_the_carbon_instance()
    {
        $value = 'January 4th, 1984';
        $field = DatetimeField::make('created_at');
        $this->assertInstanceOf(Field::class, $field->value($value));
        $this->assertInstanceOf(Carbon::class, $field->handle());
        $this->assertEquals(Carbon::parse($value)->toDateTimeString(), $field->handle()->toDateTimeString());
    }

    /** @test */
    public function it_throws_an_exception_when_value_cannot_be_parsed_into_carbon_instance()
    {
        $value = 'Not A Date Or Time';
        $field = DatetimeField::make('created_at');
        $this->assertInstanceOf(Field::class, $field->value($value));
        $this->expectException(InvalidFormatException::class);
        $field->handle();
    }

    /** @test */
    public function it_returns_null_when_value_cannot_be_parsed_into_carbon_instance_and_allow_null()
    {
        $value = 'Not A Date Or Time';
        $field = DatetimeField::make('created_at')->value($value)->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        $field = DatetimeField::make('created_at');
        $this->assertNull($field->handle());
    }
}
