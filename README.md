# Supermodels for Laravel

[![Latest Stable Version](https://poser.pugx.org/chargefield/supermodels/v/stable)](https://packagist.org/packages/chargefield/supermodels)
[![Total Downloads](https://poser.pugx.org/chargefield/supermodels/downloads)](https://packagist.org/packages/chargefield/supermodels)
[![License](https://poser.pugx.org/chargefield/supermodels/license)](https://packagist.org/packages/chargefield/supermodels)

Supermodels is a Laravel package that will help you organize your business logic.

## Installation
You can install the package via composer:
```bash
composer require chargefield/supermodels
```

## Usage
A simple example for storing a record from a controller.
```php
namespace App\Http\Controllers;

use App\Models\Post;
use Chargefield\Supermodel\Fields\SlugField;
use Chargefield\Supermodel\Fields\StringField;
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
setting data
```php
$post = Post::make()->savable()->data([...])->columns([...])->save();
```
setting data from request
```php
$post = Post::make()->savable()->fromRequest()->columns([...])->save();
```
setting data from a given request
```php
$post = Post::make()->savable()->fromRequest(request())->columns([...])->save();
```
with validation (throws Illuminate\Validation\ValidationException)
```php
$post = Post::make()->savable()->data([...])->columns([...])->validate()->save();
```
or
```php
Post::make()->savable()->data([...])->columns([...])->hasErrors(); // return bool
```
or
```php
Post::make()->savable()->data([...])->columns([...])->getErrors(); // return Illuminate\Support\MessageBag
```

Alternatively, you can define savable columns in a model.
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chargefield\Supermodel\Fields\Field;
use Chargefield\Supermodel\Traits\Savable;
use Chargefield\Supermodel\Fields\JsonField;
use Chargefield\Supermodel\Fields\SlugField;
use Chargefield\Supermodel\Fields\FileField;
use Chargefield\Supermodel\Fields\StringField;
use Chargefield\Supermodel\Fields\BooleanField;
use Chargefield\Supermodel\Fields\DatetimeField;

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
            JsonField::make('options')->nullable(),
            DatetimeField::make('published_at')->nullable(),
        ];
    }
}
```
*`savableColumns()`will get overridden by defining columns:*<br />
`Post::make()->savable()->data([...])->columns([...])->save();`

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
### Json Field:
```php
JsonField::make('options')->pretty()->depth(512);
```
### Datetime Field:
```php
DatetimeField::make('published_at');
```
### Additionally, All fields include the following methods:

*Sets the column name and default value*
```php
StringField::make('title', 'Default Title');
```
or
```php
StringField::make('title')->value('Default Title');
```
*Sets the field name if not the same as the column name*
```php
StringField::make('title')->fieldName('name');
```
*Sets the nullable flag, null will be returned if value is empty/null/exception*
```php
StringField::make('title')->nullable();
```
*Sets the validation rules for the field ([Laravel validation rules](https://laravel.com/docs/8.x/validation#available-validation-rules))*
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
*Sets a computed closure to transform the value*
```php
StringField::make('title')->transform(function ($fieldName, $fieldValue, $fieldsData) {
    return Str::title($fieldValue);
});
```

## Testing
You can run the tests with:
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