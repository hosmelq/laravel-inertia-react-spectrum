<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\OrganizationFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        OrganizationFactory::new()
            ->has(UserFactory::new([
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
            ]))
            ->createOne([
                'name' => 'Test',
            ]);
    }
}
