<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Fields\SlugField;
use Chargefield\Supermodel\Tests\TestCase;

class SlugFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_string_field_instance()
    {
        $this->assertInstanceOf(SlugField::class, SlugField::make('slug'));
    }

    /** @test */
    public function it_can_set_and_get_the_value()
    {
        $value = 'example-text';
        $field = SlugField::make('slug');
        $this->assertInstanceOf(Field::class, $field->setValue($value));
        $this->assertEquals($value, $field->handle());
    }

    /** @test */
    public function it_can_set_the_value_based_on_another_field()
    {
        $fields = [
            'title' => 'Example Text',
        ];
        $value = 'example-text';
        $field = SlugField::make('slug');
        $this->assertInstanceOf(Field::class, $field->fromField('title'));
        $this->assertEquals($value, $field->handle($fields));
    }

    /** @test */
    public function it_can_slug_the_original_value_correctly_even_if_invalid_from_field_is_given()
    {
        $fields = [
            'title' => 'Example Text',
        ];
        $value = 'example-text';
        $field = SlugField::make('slug')->setValue($fields['title']);
        $this->assertInstanceOf(Field::class, $field->fromField('body'));
        $this->assertEquals($value, $field->handle($fields));
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        $fields = [
            'title' => 'Example Text',
        ];
        $field = SlugField::make('slug')->nullable();
        $this->assertInstanceOf(Field::class, $field->fromField('body'));
        $this->assertNull($field->handle($fields));
    }
}
