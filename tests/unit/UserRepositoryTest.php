<?php

namespace Tests;

use App\Contracts\GroupRepositoryContract;
use App\Contracts\UserRepositoryContract;
use App\Exceptions\FeatureNotYetImplementedException;
use App\Exceptions\User\NotPhoneException;
use App\Exceptions\User\PhoneExistsException;
use App\Exceptions\User\UserNotFoundException;
use App\Exceptions\User\WrongLoginOrPasswordException;
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
        $phone = '+79999999996';

        $user = $userRepository->create($name, $email, $password, $phone);
        $this->assertInstanceOf(
            User::class,
            $user,
            sprintf('Ожидался объект класса %s, %s получен', User::class, get_class($user))
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
        $phone = '+7999999996';

        $user = $userRepository->create($name, $email, $password, $phone);

        /** @var \Illuminate\Hashing\HashManager */
        $hash = app('hash');

        $userFromCredintials = $userRepository->checkCredintials($email, $password);
        assertEquals($user->id, $userFromCredintials->id, 'Не прошла проверка пользователь пароль, вернулся другой пользователь');
        $this->expectException(WrongLoginOrPasswordException::class);
        $userFromCredintials = $userRepository->checkCredintials($email, '123');
    }

    public function testFindUserById()
    {
        $userRepository = $this->userRepository;
        $name = 'userName';
        $email = 'testFindUserById@mail.ru';
        $password = 'pass12345';
        $phone = '+79999999995';

        $user = $userRepository->create($name, $email, $password, $phone);

        $recievedUser = $userRepository->getById($user->id);
        $this->assertEquals($email, $recievedUser->email, 'Поиск выдал не того пользователя');
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
        $phone1 = '+79999999991';
        $phone2 = '+79999999992';
        $phone3 = '+79999999993';

        $userRepository->create($name1, $email1, $password, $phone1);
        $userRepository->create($name2, $email2, $password, $phone2);
        $userRepository->create($name3, $email3, $password, $phone3);

        $searchList1 = $userRepository->search('special');
        $this->assertCount(3, $searchList1, sprintf('Ожидалось 3 пользователя, пришло %d', $searchList1->count()));
        $searchList2 = $userRepository->search('only.ru');
        $this->assertCount(3, $searchList2, sprintf('Ожидалось 3 пользователя, пришло %d', $searchList2->count()));
        $searchList3 = $userRepository->search('@only.ru');
        $this->assertCount(2, $searchList3, sprintf('Ожидалось 2 пользователя, пришло %d', $searchList3->count()));
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
        $email2 = 'testCheckUserSoftDelete2@mail.ru';
        $password = 'pass12345';
        $phone1 = '+79999999980';
        $phone2 = '+79999999981';

        $user = $userRepository->create($name, $email, $password, $phone1);
        $user2 = $userRepository->create($name, $email2, $password, $phone2);

        $userId = $user->id;

        $deleteResult = $userRepository->delete($user);
        $this->assertTrue($deleteResult, 'Пользователь не удалился');

        $this->expectException(UserNotFoundException::class);
        $userRepository->getById($userId);

        $trashedUser = User::withTrashed()->find($userId);
        $this->assertEquals($userId, $trashedUser->id, 'Пользователь не удалился мягко');

        $trashedUser->forceDelete();
        $deleted = User::withTrashed()->find($userId);
        $this->assertNull($deleted, 'Пользователь не был удален принудительным удалением');

        $user2Id = $user2->id;
        $deleteResult = $userRepository->delete($user2Id);
        $this->assertTrue($deleteResult, 'Пользователь переданный через int не удалился');

        $this->expectException(UserNotFoundException::class);
        $userRepository->getById($userId);

        $trashedUser = User::withTrashed()->find($userId);
        $this->assertEquals($userId, $trashedUser->id, 'Пользователь переданный через int  не удалился мягко');

        $trashedUser->forceDelete();
        $deleted = User::withTrashed()->find($userId);
        $this->assertNull($deleted, 'Пользователь переданный через int не был удален принудительным удалением');
    }

    public function testAddUserToGroup()
    {
        $userRepository = $this->userRepository;
        /** @var GroupRepositoryContract */
        $groupRepository = app()->make(GroupRepositoryContract::class);
        $user = $userRepository->create('UserToGroup', 'usertogroup@mail.ru', '12321qwe!@', '+79999999982');
        $group = $groupRepository->create('groupToAdd');
        $group2 = $groupRepository->create('groupToAdd2');
        $userRepository->addToGroup($user, $group);
        $userRepository->addToGroup($user, $group2);

        $resultUser = $userRepository->getById($user->id);
        $relatedGroups = $resultUser->groups;
        $this->assertCount(1, $relatedGroups->filter(fn ($v) => $v->id === $group->id));
        $this->assertCount(1, $relatedGroups->filter(fn ($v) => $v->id === $group2->id));
    }

    public function testPhoneValid()
    {
        $userRepository = $this->userRepository;
        $userRepository->create(
            'testPhoneValid',
            'testPhoneValid@mail.com',
            'testPhoneValidpass',
            '+79999999986'
        );
        $this->expectException(PhoneExistsException::class);
        $userRepository->create(
            'testPhoneValid2',
            'testPhoneValid2@mail.com',
            'testPhoneValidpass',
            '+79999999986'
        );
        $this->expectException(NotPhoneException::class);
        $userRepository->create(
            'testPhoneValid',
            'testPhoneValid@mail.com',
            'testPhoneValidpass',
            '89999999985'
        );
    }
}
