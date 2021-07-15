<?php
namespace Tests;

use App\Exceptions\WrongLoginOrPasswordException;
use App\Models\User;
use App\Repositories\UserRepository;

use function PHPUnit\Framework\assertEquals;

class UserRepositoryTest extends \Codeception\Test\Unit
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
        $userSevice = new UserRepository();
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

    /**
     * @return void
     */
    public function testCheckCredintials()
    {
        $userSevice = new UserRepository();
        $name = 'userName';
        $email = 'user2@mail.ru';
        $password = 'pass12345';
        
        $user = $userSevice->create($name, $email, $password);
        $this->assertInstanceOf(
            User::class,
            $user,
            sprintf('Expected object of class %s, %s got', User::class, get_class($user))
        );

        /** @var \Illuminate\Hashing\HashManager */
        $hash = app('hash');

        $userFromCredintials = $userSevice->checkCredintials($email, $password);
        assertEquals($user->id, $userFromCredintials->id, 'Wrong user from credintials');
        $this->expectException(WrongLoginOrPasswordException::class);
        $userFromCredintials = $userSevice->checkCredintials($email, '123');
    }
}