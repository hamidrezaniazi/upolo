<?php

namespace Hamidrezaniazi\Upolo\Database\Factories;

use Hamidrezaniazi\Upolo\Guard;
use Hamidrezaniazi\Upolo\Models\File;
use Hamidrezaniazi\Upolo\Tests\Models\MockModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition()
    {
        $creator = Guard::getGuardClassName();

        return [
            'disk'       => 'public',
            'uuid'       => $this->faker->uuid,
            'path'       => 'public/file.png',
            'filename'   => $this->faker->unique()->word,
            'type'       => $this->faker->unique()->word,
            'mime'       => $this->faker->unique()->word,
            'creator_id' => $creator::factory(),
        ];
    }

    public function hasOwner(): self
    {
        return $this->state(function (array $attributes) {
            $owner = MockModel::factory()->create();

            return [
                'owner_id'   => $owner->getKey(),
                'owner_type' => $owner->getMorphClass(),
            ];
        });
    }

    public function hasFlag(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'flag' => $this->faker->unique()->word,
            ];
        });
    }
}
