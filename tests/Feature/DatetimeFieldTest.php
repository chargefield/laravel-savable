<?php

namespace Chargefield\Supermodels\Tests\Feature;

use Carbon\Exceptions\InvalidFormatException;
use Chargefield\Supermodels\Fields\DatetimeField;
use Chargefield\Supermodels\Tests\TestCase;
use Illuminate\Support\Carbon;

class DatetimeFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(DatetimeField::class, DatetimeField::make('created_at'));
    }

    /** @test */
    public function it_can_set_a_string_value_and_get_a_valid_carbon_instance()
    {
        $value = 'January 4th, 1984';
        $field = DatetimeField::make('created_at');
        $field->value($value);
        $date = $field->handle();
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals(Carbon::parse($value)->toDateTimeString(), $date->toDateTimeString());
    }

    /** @test */
    public function it_can_set_an_invalid_string_value_and_throws_an_exception()
    {
        $value = 'Not A Date Or Time';
        $field = DatetimeField::make('created_at');
        $field->value($value);
        $this->expectException(InvalidFormatException::class);
        $field->handle();
    }

    /** @test */
    public function it_returns_null_when_nullable_and_an_invalid_string_value_is_set()
    {
        $value = 'Not A Date Or Time';
        $field = DatetimeField::make('created_at');
        $field->nullable();
        $field->value($value);
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        $field = DatetimeField::make('created_at');
        $this->assertNull($field->handle());
    }
}
