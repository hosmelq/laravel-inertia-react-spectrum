<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;

it('correctly casts attributes', function (): void {
    $user = UserFactory::new()->makeOne();

    expect($user->email_verified_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and(Hash::needsRehash($user->password))->toBeFalse();
});
