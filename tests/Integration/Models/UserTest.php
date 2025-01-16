<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;

it('correctly loads relationships', function (): void {
    $user = User::factory()
        ->has(Organization::factory()->count(2))
        ->createOne();

    expect($user)
        ->currentOrganization->toBeInstanceOf(Organization::class)
        ->organizations->toHaveCount(2)->each->toBeInstanceOf(Organization::class);
});

it('checks is a user belongs to an organization', function (): void {
    $organization = Organization::factory()->createOne();
    $user = User::factory()->createOne();

    expect($user->belongsToOrganization($organization))->toBeFalse();

    $organization->users()->attach($user);

    $user->refresh();

    expect($user->belongsToOrganization($organization))->toBeTrue();
});

it('sets current_organization_id when loading currentOrganization if it not already set', function (): void {
    $user = User::factory()
        ->withOrganization()
        ->createOne();

    expect($user->current_organization_id)->toBeNull();

    $user->currentOrganization;

    expect($user->fresh()->current_organization_id)->not->toBeNull();
});

it('cannot switch to an unrelated organization', function (): void {
    $organization = Organization::factory()->createOne();

    $user = User::factory()
        ->withOrganization()
        ->createOne();

    expect($user)
        ->switchOrganization($organization)->toBeFalse()
        ->currentOrganization->isNot($organization);
});

it('can switch to a related organization', function (): void {
    $organization = Organization::factory()->createOne();

    $user = User::factory()
        ->withOrganization($organization)
        ->createOne();

    expect($user)
        ->switchOrganization($organization)->toBeTrue()
        ->currentOrganization->is($organization);
});
