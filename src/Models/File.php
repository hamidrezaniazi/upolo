<?php

namespace Hamidrezaniazi\Upolo\Models;

use Hamidrezaniazi\Upolo\Contracts\HasFileInterface;
use Hamidrezaniazi\Upolo\Guard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Support\Str;

/**
 * Class File
 * @package Hamidrezaniazi\Upolo\Models
 *
 * @property integer id
 * @property string uuid
 * @property string path
 * @property string disk
 * @property string filename
 * @property string type
 * @property string flag
 * @property string mime
 * @property HasFileInterface owner
 * @property User creator
 */
class File extends Model
{
    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Guard::getGuardClassName());
    }

    /**
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param User $creator
     * @param UploadedFile $uploadedFile
     * @param HasFileInterface $owner
     * @param string $disk
     * @param string|null $type
     * @param string|null $flag
     * @return File
     */
    public function upload(
        User $creator,
        UploadedFile $uploadedFile,
        ?HasFileInterface $owner = null,
        ?string $disk = 'local',
        ?string $type = null,
        ?string $flag = null
    ): File {
        $uuid = Str::uuid();
        $path = sprintf('%s/%s', $creator->getKey(), $uuid);
        $path = $uploadedFile->store($path, $disk);

        $file = new self();
        $file->uuid = $uuid;
        $file->creator()->associate($creator);
        $file->owner()->associate($owner);
        $file->filename = $uploadedFile->getClientOriginalName();
        $file->mime = $uploadedFile->getClientMimeType();
        $file->disk = $disk;
        $file->type = $type;
        $file->flag = $flag;
        $file->path = $path;
        $file->save();
        return $file;
    }
}
