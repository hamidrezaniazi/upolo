<?php

namespace Hamidrezaniazi\Upolo\Models;

use Hamidrezaniazi\Upolo\Contracts\HasFileInterface;
use Hamidrezaniazi\Upolo\Filters\FileFilters;
use Hamidrezaniazi\Upolo\Guard;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class File.
 *
 * @property int id
 * @property string uuid
 * @property string path
 * @property string disk
 * @property string filename
 * @property string type
 * @property string flag
 * @property string mime
 * @property string url
 * @property HasFileInterface owner
 * @property User creator
 */
class File extends Model
{
    protected $appends = ['url'];

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
     * Apply all relevant thread filters.
     *
     * @param Builder $query
     * @param FileFilters $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, FileFilters $filters): Builder
    {
        return $filters->apply($query);
    }

    /**
     * @param Builder $query
     * @param HasFileInterface $owner
     * @return Builder
     */
    public function scopeWhereOwnerIs(Builder $query, HasFileInterface $owner): Builder
    {
        return $query->where('owner_type', $owner->getMorphClass())->where('owner_id', $owner->getKey());
    }

    /**
     * @param Builder $query
     * @param int $ownerId
     * @return Builder
     */
    public function scopeWhereOwnerIdIs(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * @param Builder $query
     * @param string $ownerType
     * @return Builder
     */
    public function scopeWhereOwnerTypeIs(Builder $query, string $ownerType): Builder
    {
        return $query->where('owner_type', $ownerType);
    }

    /**
     * @param Builder $query
     * @param User $creator
     * @return Builder
     */
    public function scopeWhereCreatorIs(Builder $query, User $creator): Builder
    {
        return $query->where('creator_id', $creator->getKey());
    }

    /**
     * @param Builder $query
     * @param int $creatorId
     * @return Builder
     */
    public function scopeWhereCreatorIdIs(Builder $query, int $creatorId): Builder
    {
        return $query->where('creator_id', $creatorId);
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
        ?string $disk = 'public',
        ?string $type = null,
        ?string $flag = null
    ): self {
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

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
