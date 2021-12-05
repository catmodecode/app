<?php

namespace App\Providers;

use App\Contracts\UserRepositoryContract;
use App\Models\User;
use App\Repositories\TokenRepository;
use App\Repositories\UserRepository;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function (Request $request): ?User {
            $token = $request->headers->get('authorization');
            if (!isset($token)) {
                return null;
            }
            /** @var JwtService $jwtService */
            $jwtService = app()->make(JwtService::class);
            return $jwtService->getUserFromToken($token);
        });

        Gate::define('edit-users', function (User $user, User $editUser) {
            return ($user->id === $editUser->id) || ($user->isAdmin());
        });
    }
}
