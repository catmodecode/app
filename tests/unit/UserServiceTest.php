<?php
namespace Tests;

use App\Models\User;
use App\Services\UserService;

class UserServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \Tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    /**
     * @return void
     */
    public function testCreateNewUser()
    {
        $userSevice = new UserService();
        $name = 'userName';
        $email = 'user@mail.ru';
        $password = 'pass12345';
        
        $user = $userSevice->create($name, $email, $password);
        $this->assertInstanceOf(
            User::class,
            $user,
            sprintf('Expected object of class %s, %s got', User::class, get_class($user))
        );
    }
}