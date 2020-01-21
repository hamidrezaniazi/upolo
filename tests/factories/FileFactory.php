<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Hamidrezaniazi\Upolo\Tests\Models\User;
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
        'path'       => 'public/',
        'filename'   => $faker->word,
        'creator_id' => factory(User::class),
    ];
});

$factory->state(\Hamidrezaniazi\Upolo\Models\File::class, 'has_owner', function () {
    $owner = \factory(\Hamidrezaniazi\Upolo\Tests\Models\MockModel::class)->create();

    return [
        'owner_id'   => $owner->getKey(),
        'owner_type' => $owner->getMorphClass(),
    ];
});
