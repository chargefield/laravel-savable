# Supermodels for Laravel

[![Latest Stable Version](https://poser.pugx.org/chargefield/supermodels/v/stable)](https://packagist.org/packages/chargefield/supermodels)
[![Total Downloads](https://poser.pugx.org/chargefield/supermodels/downloads)](https://packagist.org/packages/chargefield/supermodels)
[![License](https://poser.pugx.org/chargefield/supermodels/license)](https://packagist.org/packages/chargefield/supermodels)
[![Tests](https://github.com/chargefield/supermodels/actions/workflows/main.yml/badge.svg)](https://github.com/chargefield/supermodels/actions/workflows/main.yml)

Supermodels is a Laravel package that will help you organize your business logic.

## Installation

**You can install the package via composer:**
```bash
composer require chargefield/supermodels
```

## Usage

### Savable Trait
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chargefield\Supermodels\Traits\Savable;

class Post extends Model
{
    use Savable;
}
```

### Example
**A simple example for storing a record from a controller:**
```php
namespace App\Http\Controllers;

use App\Models\Post;
use Chargefield\Supermodels\Fields\SlugField;
use Chargefield\Supermodels\Fields\StringField;
use Illuminate\Http\Request;

class PostController
{
    public function store(Request $request)
    {
        $post = Post::make()->savable($request->all())->columns([
            StringField::make('title'),
            SlugField::make('slug')->fromField('title'),
            StringField::make('body'),
        ])->save();
    }
}
```

### Savable Columns
**Setting columns:**
```php
$post = Post::make()->savable()->data([...])->columns([
    StringField::make('title'),
    SlugField::make('slug')->fromField('title'),
    StringField::make('body'),
])->save();
```
**Alternatively, you can set savable columns in a model:**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chargefield\Supermodels\Fields\Field;
use Chargefield\Supermodels\Traits\Savable;
use Chargefield\Supermodels\Fields\JsonField;
use Chargefield\Supermodels\Fields\SlugField;
use Chargefield\Supermodels\Fields\FileField;
use Chargefield\Supermodels\Fields\StringField;
use Chargefield\Supermodels\Fields\BooleanField;
use Chargefield\Supermodels\Fields\IntegerField;
use Chargefield\Supermodels\Fields\DatetimeField;

class Post extends Model
{
    use Savable;

    /**
     * @return Field[]
     */
    public function savableColumns(): array
    {
        return [
            StringField::make('title')->rules('required|string'),
            SlugField::make('slug')->fromField('title'),
            StringField::make('body')->rules('required|string'),
            FileField::make('image')->nullable()->rules('nullable|image'),
            BooleanField::make('is_featured')->rules('required|boolean'),
            IntegerField::make('order')->strict()->rules('required|integer|min:1'),
            JsonField::make('options')->nullable(),
            DatetimeField::make('published_at')->nullable(),
        ];
    }
}
```
**NOTE:** *`savableColumns()` will get overridden by `columns([...])`*

### Savable Data
**Setting data:**
```php
$post = Post::make()->savable()->data([...])->columns([...])->save();
```
**Setting data from request:**
```php
$post = Post::make()->savable()->fromRequest()->columns([...])->save();
```
**Setting data from a given request:**
```php
$post = Post::make()->savable()->fromRequest(request())->columns([...])->save();
```

### Validation
**Validating before saving** *(throws Illuminate\Validation\ValidationException):*
```php
$post = Post::make()->savable()->data([...])->columns([...])->validate()->save();
```
**Validating without throwing an exception:**
```php
Post::make()->savable()->data([...])->columns([...])->hasErrors();
// return bool
```
or
```php
Post::make()->savable()->data([...])->columns([...])->getErrors();
// return Illuminate\Support\MessageBag
```
**NOTE:** *[Fields](https://github.com/chargefield/supermodels#fields) must set `rules([...])` in order to validate their data.*

## Fields

### String Field:
```php
StringField::make('title');
```

### Slug Field:
```php
SlugField::make('slug')->fromField('title')->separateBy('-');
```

### File Field:
```php
FileField::make('image')->disk('local')->path('images')->withOriginalName();
```

### Boolean Field:
```php
BooleanField::make('is_featured');
```

### Integer Field:
```php
IntegerField::make('age')->strict();
```

### Json Field:
```php
JsonField::make('options')->pretty()->depth(512);
```

### Datetime Field:
```php
DatetimeField::make('published_at');
```

### Additional Methods:
**Sets the column name and default value:**
```php
StringField::make('title', 'Default Title');
```
or
```php
StringField::make('title')->value('Default Title');
```
**Sets the field name if not the same as the column name:**
```php
StringField::make('title')->fieldName('name');
```
**Sets the nullable flag, null will be returned if value is empty/null/exception:**
```php
StringField::make('title')->nullable();
```
**Sets the validation rules for the field ([Laravel validation rules](https://laravel.com/docs/8.x/validation#available-validation-rules)):**
```php
StringField::make('user_id')->rules('required|exists:users,id');
```
or
```php
StringField::make('user_id')->rules([
    'required',
    Rule::exists('users', 'id'),
]);
```
**Sets a closure to transform the value:**
```php
StringField::make('title')->transform(function ($fieldName, $fieldValue, $fieldsData) {
    return Str::title($fieldValue);
});
```

### Custom Fields
You can create custom fields with ease using the artisan command.
```bash
php artisan make:field CustomField
```
**Outputs:**
```php
namespace App\Fields;

use Chargefield\Supermodels\Fields\Field; 

class CustomField extends Field
{
    /**
     * @param array $data
     * @return mixed
     */
    public function handle(array $data = [])
    {
        if (empty($this->value) && $this->nullable) {
            return null;
        }
        
        // Logic goes here

        return $this->value;
    }
}
```
**Testing custom fields:**

*Field::assertHandle*
```php
$field = CustomField::fake('title');
$field->value('Example Title');
$field->assertHandle('Example Title'); // passed
$field->assertHandle('Not The Same'); // failed
```
*Field::assertTransform*
```php
$field = CustomField::fake('title');
$field->value('Example Title');
$field->transform(function ($name, $value, $data) {
    return "{$data['prefix']} {$value}";
});
$field->assertTransform('Prefixed Example Title', ['prefix' => 'Prefixed']); // passed
$field->assertTransform('Example Title', ['prefix' => 'Prefixed']); // failed
```
*Field::assertValidation*
```php
$field = CustomField::fake('title');
$field->rules('required|string');
$field->assertValidation('Example Text'); // passed
$field->assertValidation(''); // failed
```

## Testing

**You can run the tests with:**
```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email support@chargefield.com instead of using the issue tracker.

## Credits

-   [Clayton D'Mello](https://github.com/chargefield)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.