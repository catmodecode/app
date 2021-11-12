<?php

namespace App\Repositories;

use App\Contracts\TokenRepositoryContract;
use App\Exceptions\User\TokenNotFoundException;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;

class TokenRepository implements TokenRepositoryContract
{
    public function create(string $token, User|int $user, Carbon $expire): Token
    {
        $user_id = is_int($user) ? $user : $user->id;
        $refresh = Token::create(['token' => $token, 'delete_at' => $expire, 'user_id' => $user_id]);

        return $refresh;
    }

    /**
     * Использовать и удалить токен
     *
     * @param string $token
     * @return Token
     * 
     * @throws TokenNotFoundException
     */
    public function useToken(string $token): Token
    {
        $found = Token::whereToken($token)->first();
        if (!isset($found)) {
            throw new TokenNotFoundException();
        }
        $found->delete();
        return $found;
    }

    public function clearExpired(): void
    {
        Token::where('delete_at', '<', Carbon::now('UTC'))->delete();
    }
}
