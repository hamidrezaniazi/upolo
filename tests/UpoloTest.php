<?php

namespace Hamidrezaniazi\Upolo\Tests;

use Hamidrezaniazi\Upolo\Models\File;
use Hamidrezaniazi\Upolo\Tests\Models\MockModel;
use Hamidrezaniazi\Upolo\Tests\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpoloTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var File
     */
    private $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file = $this->app->make(File::class);
    }

    /**
     * @test
     */
    public function itCanUploadFile()
    {
        Storage::fake();
        $user = factory(User::class)->create();
        $owner = factory(MockModel::class)->create();
        $filename = $this->faker->word;
        $type = $this->faker->word;
        $flag = $this->faker->word;
        $disk = 'local';
        $uploadedFile = UploadedFile::fake()->create($filename);
        $file = $this->file->upload($user, $uploadedFile, $owner, $disk, $type, $flag);
        $path = sprintf('%s/%s/%s', $user->getKey(), $file->uuid, $uploadedFile->hashName());
        $this->assertDatabaseHas(
            'files',
            [
                'creator_id' => $user->getKey(),
                'owner_type' => $owner->getMorphClass(),
                'owner_id'   => $owner->getKey(),
                'filename'   => $filename,
                'mime'       => $uploadedFile->getClientMimeType(),
                'disk'       => $disk,
                'type'       => $type,
                'flag'       => $flag,
                'path'       => $path,
            ]
        );
        $this->assertNotNull($file->uuid);
        Storage::disk($disk)->assertExists($path);
    }

    /**
     * @test
     */
    public function itCanUploadFileWithoutRequiredData()
    {
        Storage::fake();
        $user = factory(User::class)->create();
        $filename = $this->faker->word;
        $uploadedFile = UploadedFile::fake()->create($filename);
        $file = $this->file->upload($user, $uploadedFile);
        $path = sprintf('%s/%s/%s', $user->getKey(), $file->uuid, $uploadedFile->hashName());
        $this->assertDatabaseHas(
            'files',
            [
                'creator_id' => $user->getKey(),
                'owner_type' => null,
                'owner_id'   => null,
                'filename'   => $filename,
                'mime'       => $uploadedFile->getClientMimeType(),
                'disk'       => 'local',
                'type'       => null,
                'flag'       => null,
                'path'       => $path,
            ]
        );
        $this->assertNotNull($file->uuid);
        Storage::disk('local')->assertExists($path);
    }
}
