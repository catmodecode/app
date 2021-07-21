<?php

namespace App\Repositories;

use App\Contracts\GroupRepositoryContract;
use App\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GroupRepository implements GroupRepositoryContract
{

    /**
     * Создание группы с основными параметрами
     *
     * @param string $name
     * @return Group
     */
    public function create(string $name, string $description = ''): Group
    {
        $group = new Group(['name' => $name, 'description' => $description]);
        $group->save();
        return $group;
    }

    /**
     * Получение записи по id
     *
     * @param integer $id
     * @return Group
     */
    public function getById(int $id): Group
    {
        return Group::find($id);
    }

    /**
     * Получение списка с сортировкой и необязательной постраничкой
     *
     * @param string $sort
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function getSortedList(string $sort, string $order, int $limit = 0, int $offset = 0): Collection
    {
        $limit = $limit <= 0 ? 0 : $limit;
        $offset = $offset <= 0 ? 0 : $offset;

        $query = Group::orderBy($sort, $order);
        if ($limit > 0) {
            $query = $query->limit($limit);
            if ($offset > 0) {
                $query = $query->offset($offset);
            }
        }

        return $query->get();
    }

    /**
     * Поиск по названию
     *
     * @param string $search
     * @param integer $limit
     * @param integer $offset
     * @return Collection
     */
    public function search(string $search, int $limit = 0, int $offset = 0): Collection
    {

        $limit = $limit <= 0 ? 0 : $limit;
        $offset = $offset <= 0 ? 0 : $offset;
        $search = Str::lower($search);

        $query = Group::whereRaw('lower(name) like \'%' . $search . "%'");

        if ($limit > 0) {
            $query = $query->limit($limit);
            if ($offset > 0) {
                $query = $query->offset($offset);
            }
        }

        return $query->get();
    }

    /**
     * Изменение группы по переданным полям
     * 
     * @param Group|int $group
     * @param Collection|array $fields
     * @return Group
     */
    public function update(Group|int $group, Collection|array $fields): Group
    {
        $group = is_int($group) ? $this->getById($group) : $group;
        $fields = is_array($fields) ? collect($fields) : $fields;
        $editable = ['name', 'description'];
        $fields->filter(fn ($val, $key) => in_array($key, $editable))
            ->each(fn ($value, $key) => $group->$key = $value);
        $group->save();
        return $group;
    }

    /**
     * Удаление группы
     * 
     * @param Group|int $group
     * @return bool
     */
    public function delete(Group|int $group): bool
    {
        $group = is_int($group) ? $this->getById($group) : $group;
        return $group->delete();
    }
}
