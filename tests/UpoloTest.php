<?php

namespace Hamidrezaniazi\Upolo\Tests;

use Hamidrezaniazi\Upolo\Filters\FileFilters;
use Hamidrezaniazi\Upolo\Http\Resources\FileResource;
use Hamidrezaniazi\Upolo\Models\File;
use Hamidrezaniazi\Upolo\Tests\Models\MockModel;
use Hamidrezaniazi\Upolo\Tests\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

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
        $flag = $this->faker->word;
        $disk = 'public';
        $uploadedFile = UploadedFile::fake()->create($filename);
        $file = $this->file->upload($uploadedFile, $user, $owner, $disk, $flag);
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
                'type'       => strtok($uploadedFile->getClientMimeType(), '/'),
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
        $filename = $this->faker->word;
        $uploadedFile = UploadedFile::fake()->create($filename);
        $file = $this->file->upload($uploadedFile);
        $path = sprintf('%s/%s', $file->uuid, $uploadedFile->hashName());
        $this->assertDatabaseHas(
            'files',
            [
                'creator_id' => null,
                'owner_type' => null,
                'owner_id'   => null,
                'filename'   => $filename,
                'mime'       => $uploadedFile->getClientMimeType(),
                'disk'       => 'public',
                'type'       => strtok($uploadedFile->getClientMimeType(), '/'),
                'flag'       => null,
                'path'       => $path,
            ]
        );
        $this->assertNotNull($file->uuid);
        Storage::disk('public')->assertExists($path);
    }

    /**
     * @test
     */
    public function itCanRemoveFile()
    {
        Storage::fake();
        $filename = $this->faker->word;
        $uploadedFile = UploadedFile::fake()->create($filename);
        $file = $this->file->upload($uploadedFile);
        $file->delete();
        Storage::disk('public')->assertMissing($file->path);
        $this->assertEmpty(File::all());
    }

    /**
     * @test
     */
    public function itCanFilterFilesByOwner()
    {
        $file = factory(File::class)->state('has_owner')->create();
        factory(File::class, 5)->create();
        $this->assertEquals(1, File::whereOwnerIs($file->owner)->count());
        $this->assertTrue(File::whereOwnerIs($file->owner)->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterFilesByOwnerId()
    {
        $file = factory(File::class)->state('has_owner')->create();
        factory(File::class, 5)->create();
        $this->assertEquals(1, File::whereOwnerIdIs($file->owner->getKey())->count());
        $this->assertTrue(File::whereOwnerIdIs($file->owner->getKey())->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterFilesByOwnerType()
    {
        $file = factory(File::class)->state('has_owner')->create();
        factory(File::class, 5)->create();
        $this->assertEquals(1, File::whereOwnerTypeIs($file->owner->getMorphClass())->count());
        $this->assertTrue(File::whereOwnerTypeIs($file->owner->getMorphClass())->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterFilesByCreator()
    {
        $file = factory(File::class)->create();
        factory(File::class, 5)->create();
        $this->assertEquals(1, File::whereCreatorIs($file->creator)->count());
        $this->assertTrue(File::whereCreatorIs($file->creator)->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterFilesByCreatorId()
    {
        $file = factory(File::class)->create();
        factory(File::class, 5)->create();
        $this->assertEquals(1, File::whereCreatorIdIs($file->creator->getKey())->count());
        $this->assertTrue(File::whereCreatorIdIs($file->creator->getKey())->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterFilesByType()
    {
        $file = factory(File::class)->create();
        factory(File::class, 5)->create();
        $this->assertEquals(1, File::whereTypeIs($file->type)->count());
        $this->assertTrue(File::whereTypeIs($file->type)->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterFilesByFlag()
    {
        $file = factory(File::class)->state('has_flag')->create();
        factory(File::class, 5)->state('has_flag')->create();
        $this->assertEquals(1, File::whereFlagIs($file->flag)->count());
        $this->assertTrue(File::whereFlagIs($file->flag)->first()->is($file));
    }

    /**
     * @test
     */
    public function itShouldGenerateDownloadUrl()
    {
        $file = factory(File::class)->create();
        $this->assertEquals(Storage::disk($file->disk)->url($file->path), $file->url);
    }

    /**
     * @test
     */
    public function itShouldLoadPrimaryData()
    {
        $file = factory(File::class)->create();
        $fileResource = new FileResource($file);
        $fileResponse = $fileResource->toResponse(new Request());
        $testResponse = new TestResponse($fileResponse);
        $testResponse->assertJsonStructure(['data' => [
            'id',
            'uuid',
            'path',
            'disk',
            'filename',
            'mime',
            'creator_id',
            'creator',
            'url',
            'type',
        ]]);
    }

    /**
     * @test
     */
    public function itShouldLoadPrimaryDataWhenAllFieldArePresent()
    {
        $file = factory(File::class)->states('has_owner', 'has_flag')->create();
        $fileResource = new FileResource($file);
        $fileResponse = $fileResource->toResponse(new Request());
        $testResponse = new TestResponse($fileResponse);
        $testResponse->assertJsonStructure(['data' => [
            'id',
            'uuid',
            'path',
            'disk',
            'filename',
            'mime',
            'creator_id',
            'creator',
            'type',
            'flag',
            'owner_id',
            'owner_type',
            'owner',
            'url',
        ]]);
    }

    /**
     * @test
     */
    public function itCanFilterByOwnerTypeViaRequest()
    {
        $file = factory(File::class)->state('has_owner')->create();
        factory(File::class, 5)->create();
        $request = new Request(['owner_type' => $file->owner->getMorphClass()]);
        $filters = new FileFilters($request);
        $files = File::filter($filters)->get();
        $this->assertEquals(1, $files->count());
        $this->assertTrue($files->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterByOwnerIdViaRequest()
    {
        $file = factory(File::class)->state('has_owner')->create();
        factory(File::class, 5)->create();
        $request = new Request(['owner_id' => $file->owner->getKey()]);
        $filters = new FileFilters($request);
        $files = File::filter($filters)->get();
        $this->assertEquals(1, $files->count());
        $this->assertTrue($files->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterByCreatorIdViaRequest()
    {
        $file = factory(File::class)->create();
        factory(File::class, 5)->create();
        $request = new Request(['creator_id' => $file->creator->getKey()]);
        $filters = new FileFilters($request);
        $files = File::filter($filters)->get();
        $this->assertEquals(1, $files->count());
        $this->assertTrue($files->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterByTypeViaRequest()
    {
        $file = factory(File::class)->create();
        factory(File::class, 5)->create();
        $request = new Request(['type' => $file->type]);
        $filters = new FileFilters($request);
        $files = File::filter($filters)->get();
        $this->assertEquals(1, $files->count());
        $this->assertTrue($files->first()->is($file));
    }

    /**
     * @test
     */
    public function itCanFilterByFlagViaRequest()
    {
        $file = factory(File::class)->state('has_flag')->create();
        factory(File::class, 5)->state('has_flag')->create();
        $request = new Request(['flag' => $file->flag]);
        $filters = new FileFilters($request);
        $files = File::filter($filters)->get();
        $this->assertEquals(1, $files->count());
        $this->assertTrue($files->first()->is($file));
    }
}
