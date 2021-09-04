<?php

namespace Chargefield\Supermodels\Tests;

use Chargefield\Supermodels\Tests\Fixtures\Post;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->setUpRoutes();
    }

    protected function setUpRoutes(): void
    {
        Route::post('/posts', function (Request $request) {
            Post::make()->savable()->fromRequest($request)->validate()->save();

            return response([], 200);
        })->name('posts.store');

        Route::patch('/posts/{id}', function (Request $request, $id) {
            Post::make($id)->savable()->fromRequest($request)->validate()->save();

            return response([], 200);
        })->name('posts.update');
    }

    protected function setUpDatabase(Application $app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->json('options')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }
}
