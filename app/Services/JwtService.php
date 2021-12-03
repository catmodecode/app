<?php

namespace App\Services;

use App\Contracts\TokenRepositoryContract;
use App\Contracts\UserRepositoryContract;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Collection;

class JwtService
{
    private const ALG = 'RS256';
    private string $privateKey;
    private string $publicKey;
    private UserRepositoryContract $userRepository;
    private TokenRepositoryContract $tokenRepository;

    public function __construct()
    {
        $this->userRepository = app()->make(UserRepositoryContract::class);
        $this->tokenRepository = app()->make(TokenRepositoryContract::class);

        $this->privateKey = file_get_contents(storage_path() . '/tokens/jwtRS256.key');
        $this->publicKey = file_get_contents(storage_path() . '/tokens/jwtRS256.key.pub');
    }

    protected function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    protected function getPublicKey(): string
    {
        return $this->publicKey;
    }

    protected function getAccessExpired(): Carbon
    {
        return Carbon::now('UTC')->addSeconds(config('jwt.access_lifetime'));
    }

    protected function getRefreshExpired(): Carbon
    {
        return Carbon::now('UTC')->addSeconds(config('jwt.refresh_lifetime'));
    }

    /**
     * Получение пользователя из токена
     *
     * @param string $jwt
     * @return User
     * @throws InvalidArgumentException — Provided JWT was empty
     * @throws UnexpectedValueException — Provided JWT was invalid
     * @throws SignatureInvalidException
     * Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException
     * Provided JWT has since expired, as defined by the 'exp' claim
     */
    public function getUserFromToken(string $jwt): User
    {
        $userId = $this->decode($jwt)?->user_id;
        $userRepository = app()->make(UserRepositoryContract::class);
        return $userRepository->getById($userId);
    }

    /**
     * decode jwt token
     *
     * @param string $jwt
     * @return object — The JWT's payload as a PHP object
     * @throws InvalidArgumentException — Provided JWT was empty
     * @throws UnexpectedValueException — Provided JWT was invalid
     * @throws SignatureInvalidException
     * Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException
     * Provided JWT has since expired, as defined by the 'exp' claim
     */
    public function decode(string $jwt): object
    {
        return JWT::decode($jwt, $this->getPublicKey(), [JwtService::ALG]);
    }

    /**
     * encode payload
     * 
     * @param object|array $payload
     * @return string
     */
    public function encode(object|array $payload): string
    {
        return JWT::encode($payload, $this->getPrivateKey(), JwtService::ALG);
    }

    protected function setExp(object|array $payload, Carbon $exp): object|array
    {
        $iatStamp = Carbon::now('UTC')->timestamp;
        $expStamp = $exp->timestamp;
        if (is_array($payload)) {
            $payload['iat'] = $iatStamp;
            $payload['exp'] = $expStamp;
        } else {
            $payload->iat = $iatStamp;
            $payload->exp = $expStamp;
        };
        return $payload;
    }

    /**
     * Создание токенов access и refresh
     *
     * @param User|int $user
     * @param Collection $groups
     * @return string
     */
    public function generateAccessJwt(User|int $user): string
    {
        $user = is_int($user) ? $this->userRepository->getById($user) : $user;
        /** @var Collection */
        $groups = $user->groups->pluck('id');
        $accessPayload = [
            'user_id' => $user->id,
            'groups' => $groups->toArray()
        ];

        $accessPayload = $this->setExp($accessPayload, $this->getAccessExpired());

        $accessJwt = $this->encode($accessPayload);

        return $accessJwt;
    }

    public function generateAndStoreRefreshJwt(User|int $user): Token
    {
        $user = is_int($user) ? $this->userRepository->getById($user) : $user;

        $refreshPayload = [
            'user_id' => $user->id,
            'timestamp' => microtime(true),
        ];

        $expire = $this->getRefreshExpired();
        $refreshPayload = $this->setExp($refreshPayload, $expire);
        $refreshJwt = $this->encode($refreshPayload);

        $token = $this->tokenRepository->create($refreshJwt, $user, $expire);

        return $token;
    }

    /**
     * Использовать и удалить токен
     *
     * @param string $token
     * @return Token
     * 
     * @throws TokenNotFoundException
     * @throws ExpiredException
     * Provided JWT has since expired, as defined by the 'exp' clai
     */
    public function useToken(string $tokenText): Token
    {
        $this->decode($tokenText);
        return $this->tokenRepository->useToken($tokenText);
    }

    /**
     * Undocumented function
     *
     * @param string $jwtToken
     * @return float|int|string
     * 
     * @throws InvalidArgumentException — Provided JWT was empty
     * @throws UnexpectedValueException — Provided JWT was invalid
     * @throws SignatureInvalidException
     * Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException
     * Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException
     * Provided JWT has since expired, as defined by the 'exp' claim
     */
    public function getExpired(string $jwtToken): float|int|string
    {
        $decoded = $this->decode($jwtToken);
        return $decoded->exp;
    }
}
