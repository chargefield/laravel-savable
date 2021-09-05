<?php

namespace Chargefield\Savable\Tests\Feature;

use Chargefield\Savable\Fields\SlugField;
use Chargefield\Savable\Tests\TestCase;

class SlugFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(SlugField::class, SlugField::make('slug'));
    }

    /** @test */
    public function it_returns_a_slugged_string_when_a_valid_string_is_set()
    {
        $value = 'Example Text';
        $field = SlugField::make('slug');
        $field->value($value);
        $this->assertEquals('example-text', $field->handle());
    }

    /** @test */
    public function it_returns_a_slugged_string_when_separate_by_and_a_valid_string_is_set()
    {
        $value = 'Example Text';
        $field = SlugField::make('slug');
        $field->separateBy('_');
        $field->value($value);
        $this->assertEquals('example_text', $field->handle());
    }

    /** @test */
    public function it_returns_a_slugged_string_when_from_field_and_a_valid_string_is_set()
    {
        $fields = [
            'title' => 'Example Text',
        ];
        $value = 'example-text';
        $field = SlugField::make('slug');
        $field->fromField('title');
        $this->assertEquals($value, $field->handle($fields));
    }

    /** @test */
    public function it_returns_a_slugged_string_when_invalid_from_field_and_a_valid_string_is_set()
    {
        $fields = [
            'title' => 'Example Text',
        ];
        $value = 'example-text';
        $field = SlugField::make('slug');
        $field->fromField('body');
        $field->value($fields['title']);
        $this->assertEquals($value, $field->handle($fields));
    }

    /** @test */
    public function it_returns_null_when_nullable_and_invalid_from_field_and_a_value_is_not_set()
    {
        $fields = [
            'title' => 'Example Text',
        ];
        $field = SlugField::make('slug');
        $field->nullable();
        $field->fromField('body');
        $this->assertNull($field->handle($fields));
    }
}
