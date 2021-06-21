<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Collection;

/**
 * JwtService class
 * 
 * @method User create()
 */
class JwtService
{
    private const ALG = 'RS256';
    private string $privateKey;
    private string $publicKey;

    public function __construct()
    {
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
        return Carbon::now()->addSeconds(config('jwt.access_lifetime'));
    }

    protected function getRefreshExpired(): Carbon
    {
        return Carbon::now()->addSeconds(config('jwt.refresh_lifetime'));
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

    public function setExp(object|array $payload, Carbon $exp): object|array
    {
        $iatStamp = Carbon::now()->timestamp;
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

    public function createUserJwt(User $user, Collection $groups): Collection
    {
        $accessPayload = [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ];

        
        $refreshPayload = [
            'user_id' => $user->id
        ];
        $accessPayload = $this->setExp($accessPayload, $this->getAccessExpired());
        $refreshPayload = $this->setExp($refreshPayload, $this->getRefreshExpired());

        $accessJwt = $this->encode($accessPayload);
        $refreashJwt = $this->encode($refreshPayload);

        return collect([
            'access' => $accessJwt,
            'refresh' => $refreashJwt,
        ]);
    }
}
