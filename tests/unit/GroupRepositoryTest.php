<?php
namespace Tests;

use App\Contracts\GroupRepositoryContract;
use App\Exceptions\Group\GroupNotFoundException;
use App\Models\Group;

use function PHPUnit\Framework\assertEquals;

class GroupRepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \Tests\UnitTester
     */
    protected $tester;

    protected GroupRepositoryContract $groupRepository;
    
    protected function _before()
    {
        $this->groupRepository = app()->make(GroupRepositoryContract::class);
    }

    protected function _after()
    {
    }

    // tests
    /**
     * @return void
     */
    public function testCreateNewGroup()
    {
        $groupRepository = $this->groupRepository;
        $name = 'User group 1';
        
        $group = $groupRepository->create($name);
        $this->assertInstanceOf(
            Group::class,
            $group,
            sprintf('Expected object of class %s, %s got', Group::class, get_class($group))
        );
    }

    public function testGetById()
    {
        $groupRepository = $this->groupRepository;
        $groupName = 'get by id';
        $group = $groupRepository->create($groupName);
        $recievedGroup = $groupRepository->getById($group->id);
        $this->assertEquals($groupName, $recievedGroup->name, 'При получении по id вернулась не та группа');
    }

    public function testSearch()
    {
        $groupRepository = $this->groupRepository;
        $groupNames = collect([
            'search 1',
            'search 2',
            'search 3',
        ]);

        $groupNames->each(fn($name) => $groupRepository->create($name));
        $count = $groupRepository->search('ear');
        $this->assertCount(3, $count, 'Вернулось не 3 группы');
    }

    public function testUpdate()
    {
        $groupRepository = $this->groupRepository;
        $newName = 'Updated';
        $newDescription = 'Updated description';
        
        $group = $groupRepository->create('updateMe');
        $groupRepository->update($group, ['name' => $newName, 'description' => $newDescription]);
        $recievedGroup = $groupRepository->getById($group->id);
        $this->assertEquals($newName, $recievedGroup->name, 'Имя группы не обновилось!');
        $this->assertEquals($newDescription, $recievedGroup->description, 'Описание группы не обновилось!');
        
        $group2 = $groupRepository->create('update Me 2');
        $groupRepository->update($group2, ['name' => $newName, 'description' => $newDescription]);
        $recievedGroup2 = $groupRepository->getById($group2->id);
        $this->assertEquals($newName, $recievedGroup2->name, 'Имя группы не обновилось!');
        $this->assertEquals($newDescription, $recievedGroup2->description, 'Описание группы не обновилось!');
    }

    public function testDelete()
    {
        $groupRepository = $this->groupRepository;
        $group = $groupRepository->create('delete');
        $groupId = $group->id;
        $group->delete();

        $this->expectException(GroupNotFoundException::class);
        $groupRepository->getById($groupId);
        $trash = Group::withTrashed()->find($groupId);
        $this->assertEquals($groupId, $trash->id);
    }
}