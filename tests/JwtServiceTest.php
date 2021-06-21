<?php

use App\Services\JwtService;
use App\Services\UserService;
use Faker\Generator;
use Laravel\Lumen\Testing\DatabaseTransactions;
use PHPUnit\Framework\MockObject\MockObject;

class JwtServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function testTest()
    {
        $userService = new UserService();
        $this->assertTrue(true);
    }
}
