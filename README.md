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
with validation (if validation fails, it will throw Illuminate\Validation\ValidationException by default)
```php
$post = Post::make()->savable()->data([...])->columns([...])->validate()->save();
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
            SlugField::make('slug')->fromField('title')->separateBy('-'),
            StringField::make('body')->rules('required|string'),
            FileField::make('image')->nullable()->rules('nullable|image')->disk('public')->path('path/to/uploads')->withOriginalName(),
            BooleanField::make('is_featured')->rules('required|boolean'),
            JsonField::make('options')->nullable(),
            DatetimeField::make('published_at')->nullable(),
        ];
    }
}
```
*`savableColumns()`will get overridden by defining columns:*<br />
`Post::make()->savable()->data([...])->columns([...])->save();`

### Testing
You can run the tests with:
```bash
vendor/bin/phpunit
```

### Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security
If you discover any security related issues, please email support@chargefield.com instead of using the issue tracker.

## Credits
-   [Clayton D'Mello](https://github.com/chargefield)
-   [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.