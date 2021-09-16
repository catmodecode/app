<?php

use App\Contracts\TokenRepositoryContract;
use App\Contracts\UserRepositoryContract;
use App\Models\Token;
use App\Services\JwtService;
use Carbon\Carbon;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use PHPUnit\Framework\MockObject\MockObject;

class JwtTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected UserRepositoryContract $userRepository;
    protected JwtService $jwtService;
    protected TokenRepositoryContract $tokenRepository;

    protected function _before()
    {
        $this->userRepository = app()->make(UserRepositoryContract::class);
        $this->jwtService = app()->make(JwtService::class);
        $this->tokenRepository = app()->make(TokenRepositoryContract::class);
    }

    protected function _after()
    {
    }

    // tests
    public function testEncodeDecodeJwt()
    {
        $jwtService = $this->jwtService;
        $payload = [
            'bool' => true,
            'number' => 5,
            'string' => 'Lorem ipsum',
            'float' => 3.14,
            'array' => [
                1, 2, 3, 4, 5
            ],
            'assoc-array' => [
                'e1' => 1,
                'e2' => 2,
                'e3' => 3,
                'e4' => 4,
                'e5' => 5,
            ],
        ];

        $jwt = $jwtService->encode($payload);
        $resPayload = $jwtService->decode($jwt);
        $this->assertIsObject($resPayload);
    }

    public function testGenerateUserJwtExpired()
    {
        $this->expectException(ExpiredException::class);
        /** @var JwtService|MockObject */
        $jwtService = $this->getMockBuilder(JwtService::class)
            ->onlyMethods(['getAccessExpired'])
            ->getMock();
        $jwtService->method('getAccessExpired')
            ->willReturn(Carbon::now('UTC'));
        $userRepository = $this->userRepository;
        $testUser = $userRepository->create('testUser', 'test@mail.ru', '123qweR%');
        $userJwt = $jwtService->generateAccessJwt($testUser);
        $jwtService->decode($userJwt);
    }

    public function testGenerateUserJwtWrongSignature()
    {
        $this->expectException(SignatureInvalidException::class);
        $userRepository = new $this->userRepository;
        /** @var JwtService|MockObject */
        $jwtService = $this->getMockBuilder(JwtService::class)
            ->onlyMethods(['getPublicKey'])
            ->getMock();
        $jwtService->method('getPublicKey')->willReturnCallback(static function () {
            $rsaKey = openssl_pkey_new(array(
                "digest_alg" => OPENSSL_ALGO_SHA256,
                'private_key_bits' => 4096,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ));
            $privateKey = openssl_pkey_get_private($rsaKey);
            openssl_pkey_export($privateKey, $pem);
            $publicKeyPem = openssl_pkey_get_details($privateKey)['key'];
            return $publicKeyPem;
        });

        $testUser = $userRepository->create('testUser', 'test1@mail.ru', '123qweR%');
        $userJwt = $jwtService->generateAccessJwt($testUser, collect([]));
        $jwtService->decode($userJwt);
    }

    public function testStoreRefreshTokens()
    {
        $user = $this->userRepository
            ->create('storeRefreshToken', 'storeRefreshToken@mail.ru', '123qwe!@');
        $token = $this->jwtService->generateAndStoreRefreshJwt($user);
        $stored = Token::where('token', $token->token)->first();
        $this->assertNotNull($stored, 'Сгенерированный и сохраненный токен не найден в базе');
        $this->jwtService->useToken($token->token);
        $deleted = Token::where('token', $token->token)->first();
        $this->assertNull($deleted, 'Токен который должен быть удален, остался');
    }

    public function testExpireRefreshToken()
    {
        /** @var JwtService|MockObject */
        $jwtService = $this->getMockBuilder(JwtService::class)
            ->onlyMethods(['getRefreshExpired'])
            ->getMock();
        $jwtService->method('getRefreshExpired')
            ->willReturn(Carbon::now('UTC'));
        
        $userRepository = $this->userRepository;
        $user = $userRepository->create('testExpireRefreshToken', 'testExpireRefreshToken@mail.ru', '123qwe!@123qwe!@');
        $generatedToken = $jwtService->generateAndStoreRefreshJwt($user);
        
        $this->expectException(ExpiredException::class);
        $jwtService->useToken($generatedToken->token);
    }
}
