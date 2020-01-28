# Upolo - Laravel File Uploader

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hamidrezaniazi/upolo.svg?style=flat-square)](https://packagist.org/packages/hamidrezaniazi/upolo)
[![Build Status](https://img.shields.io/travis/hamidrezaniazi/upolo/master.svg?style=flat-square)](https://travis-ci.org/hamidrezaniazi/upolo)
[![StyleCI](https://github.styleci.io/repos/213745197/shield?branch=master)](https://github.styleci.io/repos/213745197)
[![Quality Score](https://img.shields.io/scrutinizer/g/hamidrezaniazi/upolo.svg?style=flat-square)](https://scrutinizer-ci.com/g/hamidrezaniazi/upolo)
[![Total Downloads](https://poser.pugx.org/hamidrezaniazi/upolo/downloads)](https://packagist.org/packages/hamidrezaniazi/upolo)
[![License](https://poser.pugx.org/hamidrezaniazi/upolo/license)](https://packagist.org/packages/hamidrezaniazi/upolo)

## Installation

You can install the package via composer:
```bash
composer require hamidrezaniazi/upolo
```
You can publish the migration with:
```bash
php artisan vendor:publish --provider="Hamidrezaniazi\Upolo\UpoloServiceProvider" --tag="migrations"
```
After publishing the migration you can create the files table by running the migrations:
```bash
php artisan migrate
```

## Usage
You can persist uploaded files using the facade:
``` php
Upolo::upload($uploadedFile)
```

If you want to add options in your file model during persisting, use this:
``` php
Upolo::upload($uploadedFile, $user, $owner, $disk, $flag)
```

**Owner** is related to your file with a polymorphic relation and should implement from  **HasFileInterface** and use the trait **HasFileTrait** like this:
``` php
<?php

class Owner extends Model implements HasFileInterface
{
    use HasFileTrait;
```

Available filter scopes are:
``` php
//Filter by owner
Upolo::whereOwnerIs($owner)->get();

//Filter by owner id
Upolo::whereOwnerIdIs($ownerId)->get();

//Filter by owner type
Upolo::whereOwnerTypeIs($ownerType)->get();

//Filter by creator
Upolo::whereCreatorIs($creator)->get();

//Filter by creator id
Upolo::whereCreatorIdIs($creatorId)->get();

//Filter by flag
Upolo::whereFlagIs($flag)->get();

//Filter by type
Upolo::whereTypeIs($type)->get();
```

**Type** is filled automatically based on mime type and includes image, video, application, etc.

Also you can filter files via request in your controller. The keys bellow are available for filtering that you can send via query string:
- owner_id
- owner_type
- creator_id
- type
- flag

For request filtering the index function of your controller should be like this:
``` php
<?php

class FileController extends Controller
{
    /**
     * @param FileFilters $filters
     * @return AnonymousResourceCollection
     */
    public function index(FileFilters $filters): AnonymousResourceCollection
    {
        return FileResource::collection(File::filter($filters)->paginate());
    }
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email hamidrezaniazi@yahoo.com instead of using the issue tracker.

## Credits

- [Hamidreza Niazi](https://github.com/hamidrezaniazi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
