<?php

declare(strict_types=1);

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

describe('store', function (): void {
    it('validates fields', function (array $data, array $expected): void {
        $response = post(route('register'), $data);

        $response->assertSessionHasErrors($expected);
    })
        ->with([
            'email' => [
                'data' => [
                    'email' => 'test@',
                ],
                'expected' => [
                    'email' => 'The email field must be a valid email address.',
                ],
            ],
            'email:dns' => [
                'data' => [
                    'email' => 'test@site.test',
                ],
                'expected' => [
                    'email' => 'The email field must be a valid email address.',
                ],
            ],
            'email:strict' => [
                'data' => [
                    'email' => 'test()@site.test',
                ],
                'expected' => [
                    'email' => 'The email field must be a valid email address.',
                ],
            ],
            'indisposable' => [
                'data' => [
                    'email' => 'test@0-mail.com',
                ],
                'expected' => [
                    'email' => 'Disposable email addresses are not allowed.',
                ],
            ],
            'max:255 (string)' => [
                'data' => [
                    'first_name' => Str::repeat('a', 256),
                    'last_name' => Str::repeat('a', 256),
                ],
                'expected' => [
                    'first_name' => 'The first name field must not be greater than 255 characters.',
                    'last_name' => 'The last name field must not be greater than 255 characters.',
                ],
            ],
            'min:8 (string)' => [
                'data' => [
                    'password' => 'a',
                ],
                'expected' => [
                    'password' => 'The password field must be at least 8 characters.',
                ],
            ],
            'required' => [
                'data' => [],
                'expected' => [
                    'email' => 'The email field is required.',
                    'first_name' => 'The first name field is required.',
                    'last_name' => 'The last name field is required.',
                    'password' => 'The password field is required.',
                ],
            ],
        ]);

    it('throws if a user with the given email already exists', function (): void {
        $user = User::factory()->withValidEmail()->createOne();

        $response = post(route('register'), [
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);
    });

    it('can sign up', function (): void {
        Event::fake([
            Registered::class,
        ]);

        $response = post(route('register'), [
            'email' => 'test@gmail.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'password' => 'asdf1234',
        ]);

        $response->assertRedirect('/');

        assertDatabaseHas(User::class, [
            'email' => 'test@gmail.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        Event::assertDispatched(Registered::class);

        assertAuthenticated('web');

        expect(Auth::user()->email)->toBe('test@gmail.com');
    });
});
