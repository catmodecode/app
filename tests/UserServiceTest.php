<?php

use App\Exceptions\EmailExistsException;
use App\Exceptions\NotEmailException;
use App\Exceptions\PasswordToWeakException;
use App\Models\User;
use App\Services\UserService;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use PHPUnit\Framework\MockObject\MockObject;

class UserServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function testCreateNewUser()
    {
        $userSevice = new UserService();
        /** @var Generator */
        $faker = Container::getInstance()->make(Generator::class);
        $name = $faker->userName;
        $email = $faker->email;
        $password = $faker->password($userSevice->getPasswordStrength());
        
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
    public function testFailCreateNewUserShortPasswordException()
    {
        $userSevice = new UserService();
        /** @var Generator */
        $faker = Container::getInstance()->make(Generator::class);
        $password = $faker->password(0, $userSevice->getPasswordStrength()-1);
        
        $fail = !$userSevice->validatePassword($password);

        $this->assertTrue($fail);
    }

    /**
     * @return void
     */
    public function testFailCreateNewUserNotEmail()
    {
        $userSevice = new UserService();
        /** @var Generator */
        $faker = Container::getInstance()->make(Generator::class);
        $email = $faker->word;
        
        $fail = !$userSevice->validateEmail($email);
        $this->assertTrue($fail);
    }

    /**
     * @return void
     */
    public function testFailCreateNewUserEmailExists()
    {
        /** @var MockObject|UserService */
        $userSevice = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['emailExists'])
            ->getMock();
        $userSevice->expects($this->any())
            ->method('emailExists')
            ->willReturn(true);
        
        $this->expectException(EmailExistsException::class);

        /** @var Generator */
        $faker = Container::getInstance()->make(Generator::class);
        
        $name = $faker->userName;
        $email = $faker->email;
        $password = $faker->password($userSevice->getPasswordStrength());
        
        $userSevice->create($name, $email, $password);
    }
}
