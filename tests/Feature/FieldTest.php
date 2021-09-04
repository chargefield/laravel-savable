<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Tests\Fixtures\TestField;
use Chargefield\Supermodel\Tests\TestCase;

class FieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(TestField::class, TestField::make('test'));
    }

    /** @test */
    public function it_returns_a_valid_value_when_a_valid_value_is_set()
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
    public function it_returns_a_computed_value_when_transform_is_set_to_a_closure()
    {
        $field = TestField::make('test')->value('Example Text');
        $field->transform(function ($column, $value) {
            return "Computed {$value} on {$column}";
        });
        $this->assertEquals('Computed Example Text on test', $field->compute());
    }

    /** @test */
    public function it_returns_a_computed_value_when_transform_is_set_to_a_closure_with_data_params()
    {
        $field = TestField::make('test')->value('Example Text');
        $field->transform(function ($column, $value, array $data) {
            return "{$data['prefix']} {$value} on {$column}";
        });
        $this->assertEquals('Prefixed Example Text on test', $field->compute(['prefix' => 'Prefixed']));
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
    public function it_returns_a_valid_value_when_handle_gets_called()
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
    public function it_can_get_the_field_name()
    {
        $field = TestField::make('test');
        $this->assertEquals('test', $field->getFieldName());
    }

    /** @test */
    public function it_can_set_a_field_name()
    {
        $field = TestField::make('test');
        $field->fieldName('test_key');
        $this->assertEquals('test_key', $field->getFieldName());
    }

    /** @test */
    public function it_returns_no_validation_rules()
    {
        $field = TestField::make('test');
        $this->assertFalse($field->hasRules());
        $this->assertNull($field->getRules());
    }

    /** @test */
    public function it_returns_validation_rules()
    {
        $field = TestField::make('test');
        $field->rules('required|string');
        $this->assertTrue($field->hasRules());
        $this->assertEquals('required|string', $field->getRules());
    }

    /** @test */
    public function it_returns_validation_rules_when_rules_are_set_as_an_array()
    {
        $rules = [
            'required',
            'string',
        ];
        $field = TestField::make('test');
        $field->rules($rules);
        $this->assertTrue($field->hasRules());
        $this->assertEquals($rules, $field->getRules());
    }
}
