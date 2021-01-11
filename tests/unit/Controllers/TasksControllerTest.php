<?php

namespace TaskService\Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use TaskService\Controllers\TasksController;
use TaskService\Exceptions\HttpException;
use TaskService\Framework\App;
use TaskService\Models\Customer;
use TaskService\Models\Task;
use TaskService\Repositories\TasksRepository;
use TaskService\Services\EmailService;
use TaskService\Views\TaskCompletedEmail;

class TasksControllerTest extends TestCase
{
    /** @var mixed */
    protected $app;

    protected Customer $customer;

    protected function setUp(): void
    {
        $map = [
            'getTasksRepository' => $this->createMock(TasksRepository::class),
            'getEmailService' => $this->createMock(EmailService::class),
        ];

        $this->customer = new Customer();
        $this->app = $this->createConfiguredMock(App::class, $map);
    }

    public function testGetCurrentTasks(): void
    {
        $task = new Task();
        $task->id = 42;

        $this->app->getTasksRepository()->expects($this->once())
            ->method('getCurrentTasks')
            ->with($this->customer)
            ->willReturn([$task]);

        $controller = new TasksController($this->app);

        $this->assertSame([$task], $controller->getCurrentTasks($this->customer));
    }

    public function testGetCompletedTasks(): void
    {
        $task = new Task();
        $task->id = 42;

        $this->app->getTasksRepository()->expects($this->once())
            ->method('getCompletedTasks')
            ->with($this->customer)
            ->willReturn([$task]);

        $controller = new TasksController($this->app);

        $this->assertSame([$task], $controller->getCompletedTasks($this->customer));
    }

    public function testGetTask(): void
    {
        $task = new Task();
        $task->id = 42;

        $this->app->getTasksRepository()->expects($this->once())
            ->method('getTask', 42)
            ->with($this->customer, 42)
            ->willReturn($task);

        $controller = new TasksController($this->app);

        $this->assertSame($task, $controller->getTask($this->customer, 42));
    }

    public function testGetTaskNotFound(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('task not found');

        $this->app->getTasksRepository()->expects($this->once())
            ->method('getTask', 42)
            ->with($this->customer, 42)
            ->willReturn(null);

        $controller = new TasksController($this->app);
        $controller->getTask($this->customer, 42);
    }

    public function testDeleteTask(): void
    {
        $this->app->getTasksRepository()->expects($this->once())
            ->method('deleteTask')
            ->with(42);

        $this->app->getTasksRepository()->expects($this->once())
            ->method('taskExists')
            ->with($this->customer, 42)
            ->willReturn(true);

        $controller = new TasksController($this->app);
        $controller->deleteTask($this->customer, 42);
    }

    public function testDeleteTaskNotFound(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('task not found');

        $this->app->getTasksRepository()->expects($this->once())
            ->method('taskExists')
            ->with($this->customer, 42)
            ->willReturn(false);

        $this->app->getTasksRepository()->expects($this->never())
            ->method('deleteTask');

        $controller = new TasksController($this->app);
        $controller->deleteTask($this->customer, 42);
    }

    public function testCreateTask(): void
    {
        $task = new Task();

        $this->app->getTasksRepository()->expects($this->once())
            ->method('createTask')
            ->with($this->customer, 'Test', '2020-05-22')
            ->willReturn($task);

        $controller = new TasksController($this->app);

        $this->assertSame($task, $controller->createTask($this->customer, 'Test', '2020-05-22'));
    }

    public function testCreateTaskMissingTitle(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('missing title');

        $controller = new TasksController($this->app);
        $controller->createTask($this->customer, '', '2020-05-22');
    }

    public function testCreateTaskInvalidDuedate(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('invalid duedate');

        $controller = new TasksController($this->app);
        $controller->createTask($this->customer, 'test', 'tomorrow');
    }

    public function testUpdateTask(): void
    {
        $task = new Task();
        $task->id = 42;
        $task->title = 'test';
        $task->duedate = '2020-05-22';
        $task->completed = true;

        $email = new TaskCompletedEmail();
        $email->customer = $this->customer;
        $email->task = $task;

        $this->app->getTasksRepository()->expects($this->once())
            ->method('taskExists')
            ->with($this->customer, $task->id)
            ->willReturn(true);

        $this->app->getTasksRepository()->expects($this->once())
            ->method('updateTask')
            ->with($task);

        $this->app->getEmailService()->expects($this->once())
            ->method('sendEmail')
            ->with($email);

        $controller = new TasksController($this->app);
        $controller->updateTask($this->customer, $task);
    }

    public function testUpdateTaskInvalidTitle(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('missing title');

        $task = new Task();

        $this->app->getTasksRepository()->expects($this->never())
            ->method('updateTask')
            ->with($task);

        $controller = new TasksController($this->app);
        $controller->updateTask($this->customer, $task);
    }

    public function testUpdateTaskInvalidDuedate(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('invalid duedate');

        $task = new Task();
        $task->title = 'test';
        $task->duedate = '';

        $this->app->getTasksRepository()->expects($this->never())
            ->method('updateTask')
            ->with($task);

        $controller = new TasksController($this->app);
        $controller->updateTask($this->customer, $task);
    }

    public function testUpdateTaskNotFound(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('task not found');

        $task = new Task();
        $task->id = 42;
        $task->title = 'test';
        $task->duedate = '2020-05-22';

        $this->app->getTasksRepository()->expects($this->once())
            ->method('taskExists')
            ->with($this->customer, $task->id)
            ->willReturn(false);

        $this->app->getTasksRepository()->expects($this->never())
            ->method('updateTask')
            ->with($task);

        $controller = new TasksController($this->app);
        $controller->updateTask($this->customer, $task);
    }
}
