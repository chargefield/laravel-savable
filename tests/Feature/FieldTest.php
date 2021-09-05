<?php

namespace Chargefield\Savable\Tests\Feature;

use BadMethodCallException;
use Chargefield\Savable\Fields\Field;
use Chargefield\Savable\Tests\Fixtures\TestField;
use Chargefield\Savable\Tests\TestCase;
use Exception;
use PHPUnit\Framework\AssertionFailedError;

class FieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(TestField::class, TestField::make('test'));
    }

    /** @test */
    public function it_gets_a_valid_value_when_a_valid_value_is_set()
    {
        $value = 'Example Text';
        $field = TestField::make('test');
        $field->value($value);
        $this->assertEquals($value, $field->getValue());
    }

    /** @test */
    public function it_returns_a_valid_value_when_a_valid_value_is_set()
    {
        $value = 'Example Text';
        $field = TestField::make('test');
        $field->value($value);
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
            public function handle(array $data = [])
            {
                if ($this->nullable) {
                    return 'Not Null Text';
                }

                return parent::handle($data);
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
            public function handle(array $data = [])
            {
                return "{$this->value} {$data['title']} Text";
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

    /** @test */
    public function it_throws_bad_method_call_when_not_faking()
    {
        $field = TestField::make('test');
        $field->value('Example Text');
        $this->expectException(BadMethodCallException::class);
        $field->assertHandle('Wrong Value');
    }

    /** @test */
    public function it_throws_bad_method_call_when_faking_and_calling_a_method_that_does_not_exist()
    {
        $field = TestField::fake('test');
        $field->value('Example Text');
        $this->expectException(BadMethodCallException::class);
        $field->assertMethodDoesNotExit('Wrong Value');
    }

    /** @test */
    public function it_fails_asserting_handle()
    {
        $field = TestField::fake('test');
        $field->value('Example Text');
        $this->expectException(AssertionFailedError::class);
        $field->assertHandle('Wrong Value');
    }

    /** @test */
    public function it_passes_asserting_handle()
    {
        $field = TestField::fake('test');
        $field->value('Example Text');

        try {
            $field->assertHandle('Example Text');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, "Failed asserting that the handle method returned Example Test.");
        }
    }

    /** @test */
    public function it_fails_asserting_tranform_closure()
    {
        $field = TestField::fake('test');
        $field->value('Example Text');
        $field->transform(function ($name, $value, $data) {
            return "{$data['prefix']} {$value}";
        });
        $this->expectException(AssertionFailedError::class);
        $field->assertTransform('Example Text', ['prefix' => 'Prefixed']);
    }

    /** @test */
    public function it_passes_asserting_tranform_closure()
    {
        $field = TestField::fake('test');
        $field->value('Example Text');
        $field->transform(function ($name, $value, $data) {
            return "{$data['prefix']} {$value}";
        });

        try {
            $field->assertTransform('Prefixed Example Text', ['prefix' => 'Prefixed']);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, "Failed asserting that the transform closure returned Prefixed Example Test.");
        }
    }

    /** @test */
    public function it_fails_asserting_validation_passes()
    {
        $field = TestField::fake('test');
        $field->rules('required|string');
        $this->expectException(AssertionFailedError::class);
        $field->assertValidation('');
    }

    /** @test */
    public function it_passes_asserting_validation()
    {
        $field = TestField::fake('test');
        $field->rules('required|string');

        try {
            $field->assertValidation('Example Text');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, "Failed asserting that the validation passes.");
        }
    }
}
