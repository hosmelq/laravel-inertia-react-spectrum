<?php

declare(strict_types=1);

namespace Database\Factories\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Factory
 */
trait RefreshOnCreate
{
    /**
     * {@inheritDoc}
     */
    public function create($attributes = [], ?Model $parent = null)
    {
        $models = parent::create($attributes, $parent);

        return $models instanceof Model ? $models->refresh() : $models->map->refresh();
    }
}
