<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Exceptions\InvalidImageFileException;
use Chargefield\Supermodel\Fields\FileField;
use Chargefield\Supermodel\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FileFieldTest extends TestCase
{
    /** @test */
    public function it_can_make_a_new_field_instance()
    {
        $this->assertInstanceOf(FileField::class, FileField::make('image'));
    }

    /** @test */
    public function it_can_store_a_file_when_value_is_set_to_an_uploaded_file()
    {
        Storage::fake();

        $path = 'images';
        $imageName = 'example.png';
        $value = UploadedFile::fake()->image($imageName);
        $field = FileField::make('image');
        $field->value($value);
        $this->assertEquals("{$path}/{$value->hashName()}", $field->handle());

        Storage::assertExists("{$path}/{$value->hashName()}");
    }

    /** @test */
    public function it_can_store_a_file_with_the_original_name_when_value_is_set_to_an_uploaded_file()
    {
        Storage::fake();

        $path = 'images';
        $imageName = 'example.png';
        $value = UploadedFile::fake()->image($imageName);
        $field = FileField::make('image');
        $field->withOriginalName();
        $field->value($value);
        $this->assertEquals("{$path}/{$imageName}", $field->handle());

        Storage::assertExists("{$path}/{$imageName}");
    }

    /** @test */
    public function it_can_store_a_file_to_a_given_disk_when_value_is_set_to_an_uploaded_file()
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
        $field = FileField::make('image');
        $field->withOriginalName();
        $field->disk($disk);
        $field->value($value);
        $this->assertEquals("{$path}/{$imageName}", $field->handle());

        Storage::disk($disk)->assertExists("{$path}/{$imageName}");
    }

    /** @test */
    public function it_can_store_a_file_to_a_given_path_when_value_is_set_to_an_uploaded_file()
    {
        Storage::fake();

        $path = 'path/to/uploads';
        $imageName = 'example.png';
        $value = UploadedFile::fake()->image($imageName);
        $field = FileField::make('image');
        $field->withOriginalName();
        $field->path($path);
        $field->value($value);
        $this->assertEquals("{$path}/{$imageName}", $field->handle());

        Storage::assertExists("{$path}/{$imageName}");
    }

    /** @test */
    public function it_returns_null_when_nullable_and_value_is_not_set()
    {
        Storage::fake();

        $field = FileField::make('image');
        $field->nullable();
        $this->assertNull($field->handle());
    }

    /** @test */
    public function it_throws_an_exception_when_given_an_invalid_file_value()
    {
        Storage::fake();

        $field = FileField::make('image');
        $field->value('not-a-valid-file-object');
        $this->expectException(InvalidImageFileException::class);
        $field->handle();
    }
}
