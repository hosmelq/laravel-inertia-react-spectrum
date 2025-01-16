<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\RateLimiters\LoginRateLimiter;
use App\Http\Requests\LoginRequest;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AuthenticatedSessionController
{
    /**
     * Display the login view.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('auth/login');
    }

    /**
     * Attempt to authenticate a new session.
     */
    public function store(LoginRequest $request, LoginRateLimiter $limiter): RedirectResponse
    {
        if ($limiter->tooManyAttempts($request)) {
            event(new Lockout($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $limiter->availableIn($request),
                ]),
            ]);
        }

        if (! Auth::attempt(
            $request->safe(['email', 'password']),
            $request->boolean('remember')
        )) {
            $limiter->increment($request);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $limiter->clear($request);

        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/login');
    }
}
