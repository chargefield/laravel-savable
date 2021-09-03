<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Tests\Fixtures\TestField;
use Chargefield\Supermodel\Tests\TestCase;

class FieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_test_field_instance()
    {
        $this->assertInstanceOf(TestField::class, TestField::make('test'));
    }

    /** @test */
    public function it_can_set_and_get_the_value()
    {
        $value = 'Example Text';
        $field = TestField::make('test');
        $this->assertInstanceOf(Field::class, $field->value($value));
        $this->assertEquals($value, $field->handle());
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        $field = TestField::make('test');
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_can_compute_using_a_function_to_change_value()
    {
        $field = TestField::make('test')->value('Example Text');
        $field->transform(function ($column, $value) {
            return "Computed {$value} on {$column}";
        });
        $this->assertEquals('Computed Example Text on test', $field->compute());
    }

    /** @test */
    public function it_can_compute_using_a_function_with_prefix_data_to_change_value()
    {
        $field = TestField::make('test')->value('Example Text');
        $field->transform(function ($column, $value, string $prefix = '', string $suffix = '') {
            return "{$prefix} {$value} on {$column}";
        });
        $this->assertEquals('Prefixed Example Text on test', $field->compute(['Prefixed']));
    }

    /** @test */
    public function it_can_compute_using_a_function_with_prefix_and_suffix_data_to_change_value()
    {
        $field = TestField::make('test')->value('Example Text');
        $field->transform(function ($column, $value, string $prefix = '', string $suffix = '') {
            return "{$prefix} {$value} {$suffix} on {$column}";
        });
        $this->assertEquals('Prefixed Example Text Suffixed on test', $field->compute(['Prefixed', 'Suffixed']));
    }

    /** @test */
    public function it_can_set_the_nullable_flag()
    {
        $class = new class('test') extends Field {
            public function handle(array $fields = [])
            {
                if ($this->nullable) {
                    return 'Not Null Text';
                }

                return parent::handle($fields);
            }
        };
        $field = $class->value('Example Text');
        $this->assertEquals('Example Text', $field->handle());
        $field->nullable();
        $this->assertEquals('Not Null Text', $field->handle());
    }

    /** @test */
    public function it_can_handle_and_output_value()
    {
        $class = new class('test') extends Field {
            public function handle(array $fields = [])
            {
                return "{$this->value} {$fields['title']} Text";
            }
        };
        $field = $class->value('Example');
        $this->assertEquals('Example Title Text', $field->handle(['title' => 'Title']));
    }

    /** @test */
    public function it_can_get_data_key()
    {
        $field = TestField::make('test');
        $this->assertEquals('test', $field->getFieldName());
    }

    /** @test */
    public function it_can_set_and_get_data_key()
    {
        $field = TestField::make('test');
        $field->fieldName('test_key');
        $this->assertEquals('test_key', $field->getFieldName());
    }

    /** @test */
    public function it_returns_false_when_no_rules_are_set()
    {
        $field = TestField::make('test');
        $this->assertFalse($field->hasRules());
        $this->assertNull($field->getRules());
    }

    /** @test */
    public function it_can_set_the_rules_as_string()
    {
        $field = TestField::make('test');
        $field->rules('required|string');
        $this->assertTrue($field->hasRules());
        $this->assertEquals('required|string', $field->getRules());
    }

    /** @test */
    public function it_can_set_the_rules_as_an_array()
    {
        $field = TestField::make('test');
        $field->rules([
            'required',
            'string',
        ]);
        $this->assertTrue($field->hasRules());
        $this->assertEquals([
            'required',
            'string',
        ], $field->getRules());
    }
}
