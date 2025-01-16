<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use function Sentry\captureException;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

class RegisteredUserController
{
    /**
     * Display the registration view.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('auth/register');
    }

    /**
     * Create a new registered user.
     */
    public function store(RegisterRequest $request): UserResource
    {
        try {
            $user = User::query()->create($request->validated());

            event(new Registered($user));

            Auth::login($user);

            return UserResource::make($user->refresh());
        } catch (Throwable $e) {
            captureException($e);

            // TODO: handle exception.
            throw $e;
        }
    }
}
