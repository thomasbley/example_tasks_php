<?php

namespace TaskService\Tests\Integration\Repositories;

use PHPUnit\Framework\TestCase;
use TaskService\Framework\App;
use TaskService\Models\Customer;

class TasksRepositoryTest extends TestCase
{
    protected $app;
    protected $customer;
    protected $customer2;

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
        $actual = $this->app->getTasksRepository()->createTask($this->customer, 'test', '2020-05-22');
        $this->assertNotEmpty($actual->id);

        $query = '
            SELECT id, title, duedate, completed FROM task WHERE id = ? AND customer_id = ?
        ';
        $statement = $this->app->getDatabase()->prepare($query);
        $statement->execute([$actual->id, $this->customer->id]);

        // TODO improve
        $this->assertNotEmpty($statement->fetchColumn());
    }

    public function testTaskExists(): void
    {
        $repo = $this->app->getTasksRepository();

        $actual = $repo->createTask($this->customer, 'test', '2020-05-22');
        $this->assertNotEmpty($actual);

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
        $this->assertNotEmpty($actual);

        $this->assertTrue($repo->taskExists($this->customer, $actual->id));

        $repo->deleteTask($actual->id);

        $this->assertFalse($repo->taskExists($this->customer, $actual->id));
    }

    public function testGetTask(): void
    {
        $repo = $this->app->getTasksRepository();

        $actual = $repo->createTask($this->customer, 'test', '2020-05-22');
        $this->assertNotEmpty($actual);

        $this->assertTrue($actual == $repo->getTask($this->customer, $actual->id));

        $this->assertEquals(null, $repo->getTask($this->customer, 42));
    }
}
