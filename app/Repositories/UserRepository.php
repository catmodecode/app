<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryContract;
use App\Exceptions\EmailExistsException;
use App\Exceptions\FeatureNotYetImplementedException;
use App\Exceptions\NotEmailException;
use App\Exceptions\PasswordToWeakException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\User\WrongLoginOrPasswordException;
use App\Models\User;
use Exception;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * UserRepository class
 */
class UserRepository implements UserRepositoryContract
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

    public function getById(int $id): User
    {
        /** @var User */
        $user = User::find($id);

        if (!isset($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function getSortedList(string $sort, string $order, int $limit = 0, int $offset = 0): Collection
    {
        $limit = $limit <= 0 ? 0 : $limit;
        $offset = $offset <= 0 ? 0 : $offset;

        $query = User::orderBy($sort, $order);
        if ($limit > 0) {
            $query = $query->limit($limit);
            if ($offset > 0) {
                $query = $query->offset($offset);
            }
        }

        return $query->get();
    }

    public function search(string $search, int $limit = 0, int $offset = 0): Collection
    {
        $limit = $limit <= 0 ? 0 : $limit;
        $offset = $offset <= 0 ? 0 : $offset;
        $search = Str::lower($search);

        $query = User::whereRaw('lower(email) like \'%' . $search . "%'")
            ->orWhereRaw('lower(name) like \'%' . $search . "%'");

        if ($limit > 0) {
            $query = $query->limit($limit);
            if ($offset > 0) {
                $query = $query->offset($offset);
            }
        }

        return $query->get();
    }

    public function update(User|int $user, Collection|array $fields): User
    {
        throw new FeatureNotYetImplementedException('You cant edit users right now');
        
        return $user;
    }

    public function delete(User|int $user): bool
    {
        $user = is_int($user) ? $this->getById($user) : $user;
        return $user->delete();
    }
}
