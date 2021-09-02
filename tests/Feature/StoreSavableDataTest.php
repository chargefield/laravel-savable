<?php

namespace Chargefield\Supermodel\Tests\Feature;

use Chargefield\Supermodel\Tests\Fixtures\Post;
use Chargefield\Supermodel\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class StoreSavableDataTest extends TestCase
{
    /** @test */
    public function it_creates_data_with_required_fields()
    {
        $payload = [
            'title' => 'Example Text',
            'body' => 'An example body text.',
        ];

        $response = $this->post(route('posts.store'), $payload);

        $response->assertSuccessful();

        $this->assertDatabaseHas('posts', array_merge(
            $payload,
            [
                'slug' => 'example-text',
                'image' => null,
                'is_featured' => false,
                'published_at' => null,
            ]
        ));
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_creates_data_with_all_fields()
    {
        Carbon::setTestNow();
        Storage::fake();

        $file = UploadedFile::fake()->image('example.png');
        $options = ['excerpt' => 'A short description.', 'is_visible' => true];

        $payload = [
            'title' => 'Example Text',
            'body' => 'An example body text.',
            'image' => $file,
            'is_featured' => true,
            'options' => $options,
            'published_at' => now(),
        ];

        $response = $this->post(route('posts.store'), $payload);

        $response->assertSuccessful();

        $this->assertDatabaseHas('posts', array_merge(
            $payload,
            [
                'slug' => 'example-text',
                'image' => "images/{$file->hashName()}",
                'options' => json_encode($options),
            ]
        ));
        $this->assertDatabaseCount('posts', 1);
    }

    /** @test */
    public function it_updates_data_with_all_fields()
    {
        Carbon::setTestNow();
        Storage::fake();

        $file = UploadedFile::fake()->image('example.png');
        $options = ['excerpt' => 'A short description.', 'is_visible' => true];

        $post = Post::create([
            'title' => 'Example Text',
            'slug' => 'example-text',
            'body' => 'An example body text.',
            'image' => null,
            'is_featured' => false,
            'options' => null,
            'published_at' => null,
        ]);

        $payload = [
            'title' => 'Updated Example Text',
            'body' => 'An updated example body text.',
            'image' => $file,
            'is_featured' => true,
            'options' => $options,
            'published_at' => now(),
        ];

        $response = $this->patch(route('posts.update', ['id' => $post->id]), $payload);

        $response->assertSuccessful();

        $this->assertDatabaseHas('posts', array_merge(
            $payload,
            [
                'slug' => 'updated-example-text',
                'image' => "images/{$file->hashName()}",
                'options' => json_encode($options),
            ]
        ));
        $this->assertDatabaseCount('posts', 1);
    }
}