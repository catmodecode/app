<?php

namespace App\Contracts;

use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;

interface TokenRepositoryContract
{
    /**
     * Создает и сохраняет токен обновления
     * 
     * @param User|int $user
     * @param string $token
     * @return Token
     */
    public function create(string $token, User|int $user, Carbon $expire): Token;

    /**
     * @param string $token
     * @return Token
     */
    public function useToken(string $token): Token;

    /**
     * Vacuum all old tokens
     *
     * @return void
     */
    public function clearExpired(): void;
}
