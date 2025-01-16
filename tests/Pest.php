<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;
use function Pest\Laravel\freezeSecond;

use App\Models\Organization;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->beforeEach(function (): void {
        freezeSecond();
    })
    ->in('Feature', 'Integration', 'Unit');

function login(?Authenticatable $user = null, ?Organization $organization = null): User
{
    $user ??= User::factory()
        ->when($organization, function (UserFactory $factory, Organization $organization): UserFactory {
            return $factory->withOrganization($organization);
        })
        ->createOne();

    actingAs($user);

    return $user;
}
