<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @property-read string $public_id
 */
trait HasPublicId
{
    use HasUlids;

    /**
     * Find a model by its public ID.
     */
    public static function findByPublicId(string $publicId): ?self
    {
        return static::query()->where('public_id', $publicId)->first();
    }

    /**
     * Find a model by its public ID or throw an exception.
     *
     * @throws ModelNotFoundException<$this>
     */
    public static function findOrFailByPublicId(string $publicId): self
    {
        return static::query()->where('public_id', $publicId)->firstOrFail();
    }

    /**
     * Get the unique identifiers for the model.
     *
     * @return string[]
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
