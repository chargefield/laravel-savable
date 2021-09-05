<?php

namespace Chargefield\Savable\Tests\Fixtures;

use Chargefield\Savable\Fields\BooleanField;
use Chargefield\Savable\Fields\DatetimeField;
use Chargefield\Savable\Fields\Field;
use Chargefield\Savable\Fields\FileField;
use Chargefield\Savable\Fields\JsonField;
use Chargefield\Savable\Fields\SlugField;
use Chargefield\Savable\Fields\StringField;
use Chargefield\Savable\Traits\IsSavable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use IsSavable;

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
