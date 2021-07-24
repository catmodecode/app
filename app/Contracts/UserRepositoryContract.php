<?php

namespace App\Contracts;

use App\Exceptions\User\WrongLoginOrPasswordException;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * @method User create(string $name, string $email, string $plainPassword)
 * @method bool validateEmail(string $email)
 * @method bool emailExists(string $email)
 * @method bool validatePassword(string $password)
 * @method User checkCredintials(string $userEmail, string $password)
 * @method User getById(int $id)
 * @method Collection getSortedList(string $sort, string $order, int $limit = 0, int $offset = 0)
 * @method Collection search(string $search, int $limit = 0, int $offset = 0)
 * @method User update(User|int $user, Collection|array $fields)
 * @method bool delete(User|int $user)
 */
interface UserRepositoryContract
{
    /**
     * Создание пользователя с основными параметрами
     * 
     * @param string $name
     * @param string $email
     * @param string $plainPassword
     */
    public function create(
        string $name,
        string $email,
        string $plainPassword,
    ): User;

    /**
     * Валидация почты
     *
     * @param string $email
     * @return boolean
     */
    public function validateEmail(string $email): bool;

    /**
     * Проверка на дубликат почты
     *
     * @param string $email
     * @return boolean
     */
    public function emailExists(string $email): bool;

    /**
     * Валидация длины пароля, спец символов и т.д.
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword(string $password): bool;

    /**
     * Проверка логина и пароля и возврат пользователя с соответствующими данными
     *
     * @param string $userEmail
     * @param string $password
     * @return User
     * 
     * @throws WrongLoginOrPasswordException
     */
    public function checkCredintials(string $userEmail, string $password): User;

    /**
     * Получение пользователя по Id
     *
     * @param int $id
     * @return User
     */
    public function getById(int $id): User;

    /**
     * Отсортированый список по полю
     *
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function getSortedList(string $sort, string $order, int $limit = 0, int $offset = 0): Collection;

    /**
     * Поиск по полям
     *
     * @param string $search
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function search(string $search, int $limit = 0, int $offset = 0): Collection;

    /**
     * Изменение пользователя по переданным полям
     * 
     * @param User|int $user
     * @param Collection|array $fields
     * @return User
     */
    public function update(User|int $user, Collection|array $fields): User;

    /**
     * Удаление пользователя
     * 
     * @param User|int $user
     */
    public function delete(User|int $user): bool;

    /**
     * Добавить пользователя в группу
     * 
     * @param User|int $user
     * @param Group|int $group
     * @return bool
     */
    public function addToGroup(User|int $user, Group|int $group): void;
}
