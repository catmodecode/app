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
        /** !WARN Не забывать при добавлении нового гейта добавлять миграцию в таблицу rights 
         * В гейтах проверяем только связь пользователя с записью в rights через RightsRepository
         */

        $this->app['auth']->viaRequest('api', function (Request $request): ?User {
            $token = $request->headers->get('authorization');
            if (!isset($token) || !str_contains($token, 'Bearer')) {
                return null;
            }
            $token = substr($token, 7);
            /** @var JwtService $jwtService */
            $jwtService = app()->make(JwtService::class);
            return $jwtService->getUserFromToken($token);
        });

        Gate::define('edit-users', function (User $user, User $editUser) {
            return $user->isAdmin();
        });
    }
}
