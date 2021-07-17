<?php
namespace Tests;

use App\Contracts\UserRepositoryContract;
use App\Exceptions\FeatureNotYetImplementedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\WrongLoginOrPasswordException;
use App\Models\User;

use function PHPUnit\Framework\assertEquals;

class UserRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \Tests\UnitTester
     */
    protected $tester;

    protected UserRepositoryContract $userRepository;
    
    protected function _before()
    {
        $this->userRepository = app()->make(UserRepositoryContract::class);
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
        $userRepository = $this->userRepository;
        $name = 'userName';
        $email = 'testCreateNewUser@mail.ru';
        $password = 'pass12345';
        
        $user = $userRepository->create($name, $email, $password);
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
        $userRepository = $this->userRepository;
        $name = 'userName';
        $email = 'testCheckCredintials@mail.ru';
        $password = 'pass12345';
        
        $user = $userRepository->create($name, $email, $password);
        $this->assertInstanceOf(
            User::class,
            $user,
            sprintf('Expected object of class %s, %s got', User::class, get_class($user))
        );

        /** @var \Illuminate\Hashing\HashManager */
        $hash = app('hash');

        $userFromCredintials = $userRepository->checkCredintials($email, $password);
        assertEquals($user->id, $userFromCredintials->id, 'Wrong user from credintials');
        $this->expectException(WrongLoginOrPasswordException::class);
        $userFromCredintials = $userRepository->checkCredintials($email, '123');
    }

    public function testFindUserById()
    {
        $userRepository = $this->userRepository;
        $name = 'userName';
        $email = 'testFindUserById@mail.ru';
        $password = 'pass12345';

        $user = $userRepository->create($name, $email, $password);

        $recievedUser = $userRepository->getById($user->id);
        $this->assertEquals($email, $recievedUser->email);
    }

    public function testUserSearch()
    {
        $userRepository = $this->userRepository;
        $name1 = 'special name 1';
        $email1 = 'weee@only.ru';
        $name2 = 'special name 1';
        $email2 = 'weee2@only.ru';
        $name3 = 'plain name 1';
        $email3 = 'special@not.only.ru';
        $password = 'pass12345';

        $userRepository->create($name1, $email1, $password);
        $userRepository->create($name2, $email2, $password);
        $userRepository->create($name3, $email3, $password);

        $searchList1 = $userRepository->search('special');
        $this->assertCount(3, $searchList1);
        $searchList2 = $userRepository->search('only.ru');
        $this->assertCount(3, $searchList2);
        $searchList3 = $userRepository->search('@only.ru');
        $this->assertCount(2, $searchList3);
    }

    public function testUserEditIsRestrictedForNow()
    {
        $this->expectException(FeatureNotYetImplementedException::class);

        $user = new User();

        $this->userRepository->update($user, []);
    }

    public function testCheckUserSoftDelete()
    {
        $userRepository = $this->userRepository;
        $name = 'userName';
        $email = 'testCheckUserSoftDelete@mail.ru';
        $password = 'pass12345';

        $user = $userRepository->create($name, $email, $password);

        $userId = $user->id;

        $deleteResult = $userRepository->delete($user);
        $this->assertTrue($deleteResult);

        $this->expectException(UserNotFoundException::class);
        $userRepository->getById($userId);

        $trashedUser = User::withTrashed()->find($userId);
        $this->assertEquals($userId, $trashedUser->id);
    }
}