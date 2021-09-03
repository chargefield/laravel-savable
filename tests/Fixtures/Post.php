<?php

namespace Chargefield\Supermodel\Tests\Fixtures;

use Chargefield\Supermodel\Fields\BooleanField;
use Chargefield\Supermodel\Fields\DatetimeField;
use Chargefield\Supermodel\Fields\ImageField;
use Chargefield\Supermodel\Fields\JsonField;
use Chargefield\Supermodel\Fields\SlugField;
use Chargefield\Supermodel\Fields\StringField;
use Chargefield\Supermodel\Traits\Savable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Savable;

    protected $table = 'posts';

    protected $guarded = [];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected $dates = [
        'published_at',
    ];

    public function savableColumns(): array
    {
        return [
            StringField::make('title'),
            SlugField::make('slug')->fromField('title'),
            StringField::make('body'),
            ImageField::make('image')->nullable(),
            BooleanField::make('is_featured'),
            JsonField::make('options')->nullable(),
            DatetimeField::make('published_at')->nullable(),
        ];
    }
}
