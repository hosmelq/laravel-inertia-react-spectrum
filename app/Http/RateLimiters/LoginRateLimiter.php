<?php

declare(strict_types=1);

namespace App\Http\RateLimiters;

use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;

class LoginRateLimiter
{
    /**
     * Create a new login rate limiter instance.
     */
    public function __construct(protected RateLimiter $limiter)
    {
    }

    /**
     * Determine the number of seconds until logging in is available again.
     */
    public function availableIn(LoginRequest $request): int
    {
        return $this->limiter->availableIn($this->throttleKey($request));
    }

    /**
     * Get the number of attempts for the given key.
     */
    public function attempts(LoginRequest $request): mixed
    {
        return $this->limiter->attempts($this->throttleKey($request));
    }

    /**
     * Clear the login locks for the given user credentials.
     */
    public function clear(LoginRequest $request): void
    {
        $this->limiter->clear($this->throttleKey($request));
    }

    /**
     * Increment the login attempts for the user.
     */
    public function increment(LoginRequest $request): void
    {
        $this->limiter->hit($this->throttleKey($request), 60);
    }

    /**
     * Determine if the user has too many failed login attempts.
     */
    public function tooManyAttempts(LoginRequest $request): bool
    {
        return $this->limiter->tooManyAttempts($this->throttleKey($request), 5);
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(LoginRequest $request): string
    {
        $email = (string) $request->string('email');

        return Str::transliterate(Str::lower($email).'|'.$request->ip());
    }
}
