<?php

namespace App\Contracts;

use App\Models\Group;
use Illuminate\Support\Collection;

/**
 * @method Group create(string $name, string $description = '')
 * @method Group getById(int $id)
 * @method Collection getSortedList(string $sort, string $order, int $limit = 0, int $offset = 0)
 * @method Collection search(string $search, int $limit = 0, int $offset = 0)
 * @method Group update(Group|int $group, Collection|array $fields)
 * @method bool delete(Group|int $user)
 */
interface GroupRepositoryContract
{
    /**
     * Создание группы с основными параметрами
     *
     * @param string $name
     * @return Group
     */
    public function create(string $name, string $description = ''): Group;

    /**
     * Получение записи по id
     *
     * @param integer $id
     * @return Group
     */
    public function getById(int $id): Group;

    /**
     * Получение списка с сортировкой и необязательной постраничкой
     *
     * @param string $sort
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function getSortedList(string $sort, string $order, int $limit = 0, int $offset = 0): Collection;

    /**
     * Поиск по названию
     *
     * @param string $search
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function search(string $search, int $limit = 0, int $offset = 0): Collection;

    /**
     * Изменение группы по переданным полям
     * 
     * @param Group|int $group
     * @param Collection|array $fields
     * @return Group
     */
    public function update(Group|int $group, Collection|array $fields): Group;

    /**
     * Удаление группы
     * 
     * @param Group|int $group
     * @return bool
     */
    public function delete(Group|int $user): bool;
}
