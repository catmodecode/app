<?php

namespace App\Repositories;

use App\Exceptions\EmailExistsException;
use App\Exceptions\NotEmailException;
use App\Exceptions\PasswordToWeakException;
use App\Exceptions\WrongLoginOrPasswordException;
use App\Models\User;
use Exception;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Facades\Password;

/**
 * UserRepository class
 */
class UserRepository
{
    /**
     * @param string $name
     * @param string $email
     * @param string $plainPassword
     *
     * @return User
     *
     * @throws EmailExistsException
     * @throws NotEmailException
     */
    public function create(
        string $name,
        string $email,
        string $plainPassword,
    ): User {
        if (!$this->validateEmail($email)) {
            throw new NotEmailException();
        }
        if ($this->emailExists($email)) {
            throw new EmailExistsException();
        }
        if (!$this->validatePassword($plainPassword)) {
            throw new PasswordToWeakException();
        }
        /** @var User */
        $user = User::create(['name' => $name, 'email' => $email, 'password' => $plainPassword]);
        return $user;
    }

    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function emailExists(string $email): bool
    {
        return User::whereEmail($email)->count() > 0;
    }

    public function getPasswordStrength(): int
    {
        return 5;
    }

    public function validatePassword(string $password): bool
    {
        if (strlen($password) < $this->getPasswordStrength()) {
            return false;
        }

        return true;
    }

    /**
     * Returns user match credintials
     *
     * @param string $userEmail
     * @param string $password
     * @return User
     * 
     * @throws WrongLoginOrPasswordException
     */
    public function checkCredintials(string $userEmail, string $password): User
    {
        $user = User::whereEmail($userEmail)->first();
        
        if (!isset($user)) {
            throw new WrongLoginOrPasswordException();
        }

        /** @var HashManager */
        $hashManager = app('hash');

        if (!$hashManager->check($password, $user->password)) {
            throw new WrongLoginOrPasswordException();
        }
        
        return $user;
    }
}
