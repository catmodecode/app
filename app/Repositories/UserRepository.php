<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryContract;
use App\Exceptions\Group\GroupNotFoundException;
use App\Exceptions\User\{
    NotEmailException,
    EmailExistsException,
    NotPhoneException,
    PasswordToWeakException,
    PhoneExistsException,
    UserNotFoundException,
    WrongLoginOrPasswordException,
};
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberUtil;

/**
 * UserRepository class
 */
class UserRepository implements UserRepositoryContract
{
    /**
     * @param string $name
     * @param string $email
     * @param string $plainPassword
     * @param string phone
     *
     * @return User
     *
     * @throws EmailExistsException
     * @throws NotEmailException
     * @throws NotPhoneException
     * @throws PhoneExistsException
     * @throws PasswordToWeakException
     */
    public function create(
        string $name,
        string $email,
        string $plainPassword,
        string $phone,
    ): User {
        if (!$this->validateEmail($email)) {
            throw new NotEmailException();
        }
        if (!$this->validatePhone($phone)) {
            throw new NotPhoneException();
        }
        if (!$this->validatePassword($plainPassword)) {
            throw new PasswordToWeakException();
        }
        if ($this->emailExists($email)) {
            throw new EmailExistsException();
        }
        if ($this->phoneExists($phone)) {
            throw new PhoneExistsException();
        }

        /** @var User */
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
            'phone' => $phone,
        ]);
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

    public function phoneExists(string $phone): bool
    {
        return User::where('phone', $phone)->count() > 0;
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

    public function validatePhone(string $phone): bool
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneUtil->parse($phone);
        } catch (Exception) {
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
        $user = is_int($user) ? $this->getById($user) : $user;

        $user->fill($fields);
        $user->save();

        return $user;
    }

    public function delete(User|int $user): bool
    {
        $user = is_int($user) ? $this->getById($user) : $user;
        return $user->delete();
    }

    public function addToGroup(User|int $user, Group|int $group): void
    {
        /** @var User */
        $user = is_int($user) ? $this->getById($user) : $user;
        /** @var Group */
        $group = is_int($group) ? $group : $group->id;
        if (!isset($group)) {
            throw new GroupNotFoundException();
        }
        $user->groups()->attach($group);
        $user->save();
    }
}
