<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryContract
{
    /**
     * Create new user with base parameters
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
     * Validate email, false on failed
     *
     * @param string $email
     * @return boolean
     */
    public function validateEmail(string $email): bool;

    /**
     * Find if email already in database
     *
     * @param string $email
     * @return boolean
     */
    public function emailExists(string $email): bool;

    /**
     * Validate email for strength, special chars etc
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword(string $password): bool;

    /**
     * Returns user match credintials
     *
     * @param string $userEmail
     * @param string $password
     * @return User
     * 
     * @throws WrongLoginOrPasswordException
     */
    public function checkCredintials(string $userEmail, string $password): User;

    /**
     * Get one user by id
     *
     * @param integer $id
     * @return User
     */
    public function getById(int $id): User;

    /**
     * Sort list of all users by one of columns
     *
     * @param string $sort
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function getSortedList(string $sort, string $order, int $limit = 0, int $offset = 0): Collection;

    /**
     * Search by many fields
     *
     * @param string $search
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function search(string $search, int $limit = 0, int $offset = 0): Collection;

    /**
     * Update fields presented in $fields param
     * 
     * @param User|int $user
     * @param Collection|array $fields
     * @return User
     */
    public function update(User|int $user, Collection|array $fields): User;

    /**
     * Delete user
     * 
     * @param User|int $user
     */
    public function delete(User|int $user): bool;
}
