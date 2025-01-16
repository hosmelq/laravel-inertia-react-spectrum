<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;

it('correctly loads relationships', function (): void {
    $organization = Organization::factory()
        ->has(User::factory()->count(2))
        ->createOne();

    expect($organization)
        ->users->toHaveCount(2)->each->toBeInstanceOf(User::class);
});
