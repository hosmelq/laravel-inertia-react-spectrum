<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Database\Factories\Concerns\RefreshOnCreate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    use RefreshOnCreate;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    public function withOrganization(?Organization $organization = null, array $values = []): static
    {
        $organization ??= OrganizationFactory::new()->createOne();

        return $this->afterCreating(function (User $user) use ($organization, $values): void {
            $user->organizations()->syncWithPivotValues($organization, $values);
        });
    }

    /**
     * Indicate that the model’s email address should be a valid email address.
     */
    public function withValidEmail(): static
    {
        return $this->state(fn (): array => [
            'email' => fake()->unique()->regexify('\w{8}@gmail\.com'),
        ]);
    }
}
