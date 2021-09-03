<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Exceptions\InvalidImageFileException;
use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Fields\ImageField;
use Chargefield\Supermodel\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ImageFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_string_field_instance()
    {
        $this->assertInstanceOf(ImageField::class, ImageField::make('image'));
    }

    /** @test */
    public function it_can_set_and_get_the_value()
    {
        Storage::fake();

        $path = 'images';
        $imageName = 'example.png';
        $value = UploadedFile::fake()->image($imageName);
        $field = ImageField::make('image');
        $this->assertInstanceOf(Field::class, $field->setValue($value));
        $this->assertEquals("{$path}/{$value->hashName()}", $field->handle());

        Storage::assertExists("{$path}/{$value->hashName()}");
    }

    /** @test */
    public function it_can_store_image_with_original_name_to_default_disk()
    {
        Storage::fake();

        $path = 'images';
        $imageName = 'example.png';
        $value = UploadedFile::fake()->image($imageName);
        $field = ImageField::make('image');
        $field->withOriginalName();
        $this->assertInstanceOf(Field::class, $field->setValue($value));
        $this->assertEquals("{$path}/{$imageName}", $field->handle());

        Storage::assertExists("{$path}/{$imageName}");
    }

    /** @test */
    public function it_can_store_image_with_original_name_to_given_disk()
    {
        $disk = 'test_disk';
        Config::set("filesystems.disks.{$disk}", [
            'driver' => 'local',
            'root' => storage_path('test'),
        ]);
        Storage::fake($disk);

        $path = 'images';
        $imageName = 'example.png';
        $value = UploadedFile::fake()->image($imageName);
        $field = ImageField::make('image');
        $field->withOriginalName();
        $field->disk($disk);
        $this->assertInstanceOf(Field::class, $field->setValue($value));
        $this->assertEquals("{$path}/{$imageName}", $field->handle());

        Storage::disk($disk)->assertExists("{$path}/{$imageName}");
    }

    /** @test */
    public function it_can_store_image_with_original_name_to_given_path()
    {
        Storage::fake();

        $path = 'path/to/uploads';
        $imageName = 'example.png';
        $value = UploadedFile::fake()->image($imageName);
        $field = ImageField::make('image');
        $field->withOriginalName();
        $field->setPath($path);
        $this->assertInstanceOf(Field::class, $field->setValue($value));
        $this->assertEquals("{$path}/{$imageName}", $field->handle());

        Storage::assertExists("{$path}/{$imageName}");
    }

    /** @test */
    public function it_returns_null_when_value_is_not_set()
    {
        Storage::fake();

        $field = ImageField::make('image')->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_throws_an_exception_when_given_an_invalid_file_object()
    {
        Storage::fake();

        $field = ImageField::make('image')->setValue('not-a-valid-file-object');
        $this->expectException(InvalidImageFileException::class);
        $field->handle();
    }
}
