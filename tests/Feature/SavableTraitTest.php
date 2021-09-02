<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Exceptions\FieldNotFoundException;
use Chargefield\Supermodel\Exceptions\NoColumnsToSaveException;
use Chargefield\Supermodel\Tests\Fixtures\Post;
use Chargefield\Supermodel\Tests\Fixtures\TestField;
use Chargefield\Supermodel\Tests\TestCase;
use Chargefield\Supermodel\Traits\Savable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class SavableTraitTest extends TestCase
{
    /** @test */
    public function it_uses_the_savable_trait()
    {
        $this->assertContains('Chargefield\\Supermodel\\Traits\\Savable', class_uses(new Post));
    }

    /** @test */
    public function it_saves_data_to_the_database()
    {
        Carbon::setTestNow();

        $data = [
            'title' => 'Example',
            'slug' => 'example',
            'body' => 'An example body.',
            'is_featured' => true,
            'published_at' => Carbon::now(),
        ];

        $post = Post::make()->setPayload($data)->saveData();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', $data);
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_updates_data_in_the_database()
    {
        Carbon::setTestNow();

        $post = Post::create([
            'title' => 'Example',
            'slug' => 'example',
            'body' => 'An example body.',
            'is_featured' => true,
            'published_at' => Carbon::now()->subDay(),
        ]);

        $data = [
            'title' => 'Updated Example',
            'slug' => 'updated-example',
            'body' => 'An updated example body.',
            'is_featured' => false,
            'published_at' => Carbon::now(),
        ];

        $post->setPayload($data)->saveData();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', $data);
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_updates_data_in_the_database_using_a_model()
    {
        Carbon::setTestNow();

        $post = Post::create([
            'title' => 'Example',
            'slug' => 'example',
            'body' => 'An example body.',
            'is_featured' => true,
            'published_at' => Carbon::now()->subDay(),
        ]);

        $data = [
            'title' => 'Updated Example',
            'slug' => 'updated-example',
            'body' => 'An updated example body.',
            'is_featured' => false,
            'published_at' => Carbon::now(),
        ];

        $newPost = Post::make($post)->setPayload($data)->saveData();

        $this->assertInstanceOf(Post::class, $newPost);
        $this->assertDatabaseHas('posts', $data);
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_throws_an_exception_when_savable_columns_returns_an_empty_array()
    {
        $post = new class extends Model {
            use Savable;

            protected function savableColumns(): array
            {
                return [];
            }
        };

        $this->expectException(NoColumnsToSaveException::class);

        $post->saveData();
    }

    /** @test */
    public function it_throws_an_exception_when_savable_columns_has_an_invalid_field()
    {
        $post = new class extends Model {
            use Savable;

            protected function savableColumns(): array
            {
                return [
                    'not-a-valid-field',
                ];
            }
        };

        $this->expectException(FieldNotFoundException::class);

        $post->setPayload(['title' => 'Example Text'])->saveData();
    }

    /** @test */
    public function it_ignores_columns_that_are_not_set()
    {
        $data = [
            'title' => 'Example',
            'slug' => 'example',
            'body' => 'An example body.',
        ];

        $post = Post::make()->setPayload($data)->saveData();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', $data);
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_sets_slug_value_from_title()
    {
        $data = [
            'title' => 'Example Text',
            'body' => 'An example body.',
        ];

        $post = Post::make()->setPayload($data)->saveData();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', array_merge($data, ['slug' => 'example-text']));
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_gets_the_correct_data_when_data_key_is_set()
    {
        $data = [
            'name' => 'Example',
            'slugged' => 'example',
            'content' => 'An example body.',
        ];

        $class = new class extends Post {
            public function savableColumns(): array
            {
                return [
                    TestField::make('title')->setDataKey('name'),
                    TestField::make('slug')->setDataKey('slugged'),
                    TestField::make('body')->setDataKey('content'),
                ];
            }
        };

        $post = $class->setPayload($data)->saveData();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', [
            'title' => 'Example',
            'slug' => 'example',
            'body' => 'An example body.',
        ]);
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_throws_an_exception_when_it_fails_validation_with_invalid_data()
    {
        $data = [
            'title' => '',
            'slug' => null,
        ];

        $class = new class extends Post {
            public function savableColumns(): array
            {
                return [
                    TestField::make('title')->setRules('required|string'),
                    TestField::make('slug')->setRules('required|string'),
                ];
            }
        };

        $this->expectException(ValidationException::class);

        $class->setPayload($data)->validate();
    }

    /** @test */
    public function it_fails_validation_with_invalid_data()
    {
        $data = [
            'title' => '',
            'slug' => null,
        ];

        $class = new class extends Post {
            public function savableColumns(): array
            {
                return [
                    TestField::make('title')->setRules('required|string'),
                    TestField::make('slug')->setRules('required|string'),
                ];
            }
        };

        $class->setPayload($data)->validate(false);

        $this->assertTrue($class->hasValidationErrors());
        $this->assertCount(2, $class->getValidationErrors());
    }

    /** @test */
    public function it_passes_validation_with_valid_data_and_throws_exception_is_false()
    {
        $data = [
            'title' => 'Example',
        ];

        $class = new class extends Post {
            public function savableColumns(): array
            {
                return [
                    TestField::make('title')->setRules('required|string'),
                ];
            }
        };

        $class->setPayload($data)->validate( false);

        $this->assertFalse($class->hasValidationErrors());
        $this->assertCount(0, $class->getValidationErrors());
    }

    /** @test */
    public function it_passes_validation_with_valid_data()
    {
        $data = [
            'title' => 'Example',
        ];

        $class = new class extends Post {
            public function savableColumns(): array
            {
                return [
                    TestField::make('title')->setRules('required|string'),
                ];
            }
        };

        $class->setPayload($data)->validate();

        $this->assertFalse($class->hasValidationErrors());
        $this->assertCount(0, $class->getValidationErrors());
    }
}