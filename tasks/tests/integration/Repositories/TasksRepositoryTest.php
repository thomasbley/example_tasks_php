<?php

namespace TaskService\Tests\Integration\Repositories;

use PDO;
use PHPUnit\Framework\TestCase;
use TaskService\Framework\App;
use TaskService\Models\Customer;

class TasksRepositoryTest extends TestCase
{
    protected App $app;
    protected Customer $customer;
    protected Customer $customer2;

    public function setUp(): void
    {
        $this->app = new App([], [], $_SERVER, []);

        $this->app->getDatabase()->beginTransaction();

        $this->customer = new Customer();
        $this->customer->id = 41;

        $this->customer2 = new Customer();
        $this->customer2->id = 42;
    }

    public function tearDown(): void
    {
        $this->app->getDatabase()->rollBack();
    }

    public function testCreateTask(): void
    {
        $expected = $this->app->getTasksRepository()->createTask($this->customer, 'test', '2020-05-22');
        $this->assertNotEmpty($expected->id);

        $query = '
            SELECT id, title, duedate, completed FROM task WHERE id = ? AND customer_id = ?
        ';
        $statement = $this->app->getDatabase()->prepare($query);
        $statement->execute([$expected->id, $this->customer->id]);

        $actual = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($actual);
        $this->assertEquals($expected->id, $actual['id']);
        $this->assertEquals('test', $actual['title']);
        $this->assertEquals('2020-05-22', $actual['duedate']);
        $this->assertEquals('0', $actual['completed']);
    }

    public function testTaskExists(): void
    {
        $repo = $this->app->getTasksRepository();

        $actual = $repo->createTask($this->customer, 'test', '2020-05-22');

        $this->assertTrue($repo->taskExists($this->customer, $actual->id));
        $this->assertFalse($repo->taskExists($this->customer2, $actual->id));
        $this->assertFalse($repo->taskExists($this->customer, 42));
    }

    public function testGetCurrentTasks(): void
    {
        $repo = $this->app->getTasksRepository();

        $task1 = $repo->createTask($this->customer, 'test1', '2020-05-22');

        $task2 = $repo->createTask($this->customer, 'test2', '2020-05-23');

        $repo->createTask($this->customer2, 'test3', '2020-05-23');

        $actual = $repo->getCurrentTasks($this->customer);

        $this->assertCount(2, $actual);
        $this->assertTrue([$task1, $task2] == $actual);
    }

    public function testGetCompletedTasks(): void
    {
        $repo = $this->app->getTasksRepository();

        $task = $repo->createTask($this->customer, 'test1', '2020-05-22');
        $task->completed = true;

        $repo->updateTask($task);

        $this->assertEquals([$task], $repo->getCompletedTasks($this->customer));
    }

    public function testDeleteTask(): void
    {
        $repo = $this->app->getTasksRepository();

        $actual = $repo->createTask($this->customer, 'test', '2020-05-22');

        $this->assertTrue($repo->taskExists($this->customer, $actual->id));

        $repo->deleteTask($actual->id);

        $this->assertFalse($repo->taskExists($this->customer, $actual->id));
    }

    public function testGetTask(): void
    {
        $repo = $this->app->getTasksRepository();

        $actual = $repo->createTask($this->customer, 'test', '2020-05-22');

        $this->assertTrue($actual == $repo->getTask($this->customer, $actual->id));

        $this->assertEquals(null, $repo->getTask($this->customer, 42));
    }
}
