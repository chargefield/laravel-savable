<?php

namespace Chargefield\Supermodels\Tests\Fixtures;

use Chargefield\Supermodels\Fields\BooleanField;
use Chargefield\Supermodels\Fields\DatetimeField;
use Chargefield\Supermodels\Fields\Field;
use Chargefield\Supermodels\Fields\FileField;
use Chargefield\Supermodels\Fields\JsonField;
use Chargefield\Supermodels\Fields\SlugField;
use Chargefield\Supermodels\Fields\StringField;
use Chargefield\Supermodels\Traits\Savable;
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

    /**
     * @return Field[]
     */
    public function savableColumns(): array
    {
        return [
            StringField::make('title'),
            SlugField::make('slug')->fromField('title'),
            StringField::make('body'),
            FileField::make('image')->nullable(),
            BooleanField::make('is_featured'),
            JsonField::make('options')->nullable(),
            DatetimeField::make('published_at')->nullable(),
        ];
    }
}
