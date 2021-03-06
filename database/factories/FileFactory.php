<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Hamidrezaniazi\Upolo\Guard;
use Illuminate\Database\Eloquent\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\Hamidrezaniazi\Upolo\Models\File::class, function (Faker $faker) {
    return [
        'disk'       => 'public',
        'uuid'       => $faker->uuid,
        'path'       => 'public/file.png',
        'filename'   => $faker->unique()->word,
        'type'       => $faker->unique()->word,
        'mime'       => $faker->unique()->word,
        'creator_id' => factory(Guard::getGuardClassName()),
    ];
});

$factory->state(\Hamidrezaniazi\Upolo\Models\File::class, 'has_owner', function () {
    $owner = \factory(\Hamidrezaniazi\Upolo\Tests\Models\MockModel::class)->create();

    return [
        'owner_id'   => $owner->getKey(),
        'owner_type' => $owner->getMorphClass(),
    ];
});

$factory->state(\Hamidrezaniazi\Upolo\Models\File::class, 'has_flag', function (Faker $faker) {
    return [
        'flag' => $faker->unique()->word,
    ];
});
