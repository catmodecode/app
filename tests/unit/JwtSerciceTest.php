<?php

use App\Models\User;
use App\Services\JwtService;
use App\Services\UserService;
use Carbon\Carbon;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use PHPUnit\Framework\MockObject\MockObject;

class JwtSerciceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testEncodeDecodeJwt()
    {
        $jwtService = new JwtService();
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
            ->willReturn(Carbon::now());
        $userService = new UserService();
        $testUser = $userService->create('testUser', 'test@mail.ru', '123qweR%');
        $userJwt = $jwtService->createUserJwt($testUser, collect([]));
        $jwtService->decode($userJwt->get('access'));
    }

    public function testGenerateUserJwtWrongSignature()
    {
        $this->expectException(SignatureInvalidException::class);
        $userService = new UserService();
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

        $testUser = $userService->create('testUser', 'test1@mail.ru', '123qweR%');
        $userJwt = $jwtService->createUserJwt($testUser, collect([]));
        $jwtService->decode($userJwt->get('access'));
    }
}
