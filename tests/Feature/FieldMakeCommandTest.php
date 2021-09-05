<?php

namespace Chargefield\Savable\Tests\Feature;

use Chargefield\Savable\Tests\TestCase;
use Illuminate\Support\Facades\File;

class FieldMakeCommandTest extends TestCase
{
    /** @test */
    public function it_can_make_a_custom_field_class_using_the_command()
    {
        $type = 'Field';
        $class = "Custom{$type}";
        $path = app_path("Fields/{$class}.php");

        File::delete(app_path($path));

        $this->assertFalse(File::exists($path));

        $this->artisan('make:field', ['name' => $class])
            ->expectsOutput("{$type} created successfully.")
            ->assertExitCode(0);

        $this->assertTrue(File::exists($path));

        $this->artisan('make:field', ['name' => $class])
            ->expectsOutput("{$type} already exists!")
            ->assertExitCode(0);

        File::delete($path);
    }

    /** @test */
    public function it_can_make_a_custom_field_class_using_the_command_with_custom_stub()
    {
        $type = 'Field';
        $class = "Custom{$type}";
        $path = app_path("Fields/{$class}.php");
        $stubPath = base_path('stubs');

        File::delete($path);
        File::delete($stubPath.'/field.stub');

        File::copyDirectory(__DIR__.'/../../src/Commands/stubs', $stubPath);

        $this->assertFalse(File::exists($path));

        $this->artisan('make:field', ['name' => $class])
            ->expectsOutput("{$type} created successfully.")
            ->assertExitCode(0);

        $this->assertTrue(File::exists($path));

        File::delete($path);
        File::delete($stubPath.'/field.stub');
    }
}
