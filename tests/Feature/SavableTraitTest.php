<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Exceptions\FieldNotFoundException;
use Chargefield\Supermodel\Exceptions\NoColumnsToSaveException;
use Chargefield\Supermodel\Exceptions\NotSavableException;
use Chargefield\Supermodel\SavableModel;
use Chargefield\Supermodel\Tests\Fixtures\Post;
use Chargefield\Supermodel\Tests\Fixtures\TestField;
use Chargefield\Supermodel\Tests\TestCase;
use Chargefield\Supermodel\Traits\Savable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\MessageBag;
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

        $post = Post::make()->savable($data)->save();

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

        $post->savable($data)->save();

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

        $newPost = Post::make($post)->savable($data)->save();

        $this->assertInstanceOf(Post::class, $newPost);
        $this->assertDatabaseHas('posts', $data);
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_throws_an_exception_when_savable_columns_returns_an_empty_array()
    {
        $post = new class extends Model {
            use Savable;

            public function savableColumns(): array
            {
                return [];
            }
        };

        $this->expectException(NoColumnsToSaveException::class);

        $post->savable()->save();
    }

    /** @test */
    public function it_throws_an_exception_when_savable_columns_has_an_invalid_field()
    {
        $post = new class extends Model {
            use Savable;

            public function savableColumns(): array
            {
                return [
                    'not-a-valid-field',
                ];
            }
        };

        $this->expectException(FieldNotFoundException::class);

        $post->savable(['title' => 'Example Text'])->save();
    }

    /** @test */
    public function it_ignores_columns_that_are_not_set()
    {
        $data = [
            'title' => 'Example',
            'slug' => 'example',
            'body' => 'An example body.',
        ];

        $post = Post::make()->savable($data)->save();

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

        $post = Post::make()->savable($data)->save();

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

        $post = $class->savable($data)->save();

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

        $class->savable($data)->validate();
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

        $savable = $class->savable($data)->validate(false);

        $this->assertTrue($savable->hasValidationErrors());
        $this->assertCount(2, $savable->getValidationErrors());
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

        $savable = $class->savable($data)->validate( false);

        $this->assertFalse($savable->hasValidationErrors());
        $this->assertCount(0, $savable->getValidationErrors());
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

        $savable = $class->savable($data)->validate();

        $this->assertFalse($savable->hasValidationErrors());
        $this->assertCount(0, $savable->getValidationErrors());
    }

    /** @test */
    public function it_throws_an_exception_if_model_is_not_savable()
    {
        $class = new class extends Model {};

        $this->expectException(NotSavableException::class);

        new SavableModel($class);
    }

    /** @test */
    public function it_returns_false_if_no_validator_and_has_validation_errors_is_called()
    {
        $savable = new SavableModel(new Post);

        $this->assertFalse($savable->hasValidationErrors());
    }

    /** @test */
    public function it_returns_false_if_no_validator_and_get_validation_errors_is_called()
    {
        $savable = new SavableModel(new Post);

        $this->assertInstanceOf(MessageBag::class, $savable->getValidationErrors());
        $this->assertCount(0, $savable->getValidationErrors());
    }
}