<?php

declare(strict_types=1);

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\post;

use App\Http\RateLimiters\LoginRateLimiter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mockery\MockInterface;

describe('store', function (): void {
    it('validates fields', function (): void {
        $response = post(route('login'));

        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
        ]);
    });

    it('validations invalid credentials', function (): void {
        $response = post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    });

    it('throttles login attempts', function (): void {
        $this->mock(LoginRateLimiter::class, function (MockInterface $mock): void {
            $mock->shouldReceive('availableIn')->andReturn(10);
            $mock->shouldReceive('tooManyAttempts')->andReturn(true);
        });

        $response = post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Too many login attempts. Please try again in 10 seconds.',
        ]);
    });

    it('can log in', function (): void {
        User::factory()->createOne([
            'email' => 'test@example.com',
            'password' => 'asdf',
        ]);

        $response = post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'asdf',
        ]);

        $response->assertRedirect('/');

        assertAuthenticated();

        expect(Auth::user()->email)->toBe('test@example.com');
    });
});

describe('destroy', function (): void {
    it('can logout', function (): void {
        login();

        expect(Auth::user())->not->toBeNull();

        $response = post(route('logout'));

        $response->assertRedirectToRoute('login');

        assertGuest();
    });
});
