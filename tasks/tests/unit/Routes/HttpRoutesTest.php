<?php

namespace TaskService\Tests\Unit\Routes;

use PHPUnit\Framework\TestCase;
use TaskService\Controllers\TasksController;
use TaskService\Exceptions\HttpException;
use TaskService\Framework\App;
use TaskService\Framework\Authentication;
use TaskService\Framework\Output;
use TaskService\Framework\Router;
use TaskService\Models\Customer;
use TaskService\Models\Task;
use TaskService\Routes\HttpRoutes;
use TaskService\Serializer\TasksSerializer;

class HttpRoutesTest extends TestCase
{
    /** @var mixed */
    protected $app;

    protected Customer $customer;

    protected function setUp(): void
    {
        $customer = new Customer();

        $appMap = [
            'getAuthentication' => $this->createConfiguredMock(Authentication::class, ['getCustomer' => $customer]),
            'getOutput' => $this->createMock(Output::class),
            'getRouter' => new Router(),
            'getTasksController' => $this->createMock(TasksController::class),
            'getTasksSerializer' => $this->createMock(TasksSerializer::class),
        ];

        $this->customer = $customer;
        $this->app = $this->createConfiguredMock(App::class, $appMap);
    }

    public function testGetCurrentTasks(): void
    {
        $task = new Task();

        $this->app->method('getHeader')
            ->willReturnMap($this->getHeaders('GET', '/v1/tasks'));

        $this->app->getTasksController()->expects($this->once())
            ->method('getCurrentTasks')
            ->with($this->customer)
            ->willReturn([$task]);

        $this->app->getTasksSerializer()->expects($this->once())
            ->method('serializeTasks')
            ->with([$task])
            ->willReturn(['some-data']);

        $this->app->getOutput()->expects($this->once())
            ->method('json')
            ->with(['some-data'], 200);

        $routes = new HttpRoutes($this->app);
        $routes->run();
    }

    public function testGetCompletedTasks(): void
    {
        $task = new Task();

        $this->app->method('getHeader')
            ->willReturnMap($this->getHeaders('GET', '/v1/tasks'));

        $this->app->method('getParam')
            ->willReturnMap([['completed', '1']]);

        $this->app->getTasksController()->expects($this->once())
            ->method('getCompletedTasks')
            ->with($this->customer)
            ->willReturn([$task]);

        $this->app->getTasksSerializer()->expects($this->once())
            ->method('serializeTasks')
            ->with([$task])
            ->willReturn(['some-data']);

        $this->app->getOutput()->expects($this->once())
            ->method('json')
            ->with(['some-data'], 200);

        $routes = new HttpRoutes($this->app);
        $routes->run();
    }

    public function testDeleteTask(): void
    {
        $this->app->method('getHeader')
            ->willReturnMap($this->getHeaders('DELETE', '/v1/tasks/123'));

        $this->app->getTasksController()->expects($this->once())
            ->method('deleteTask')
            ->with($this->customer, '123');

        $this->app->getOutput()->expects($this->once())
            ->method('noContent');

        $routes = new HttpRoutes($this->app);
        $routes->run();
    }

    public function testGetTask(): void
    {
        $task = new Task();

        $this->app->method('getHeader')
            ->willReturnMap($this->getHeaders('GET', '/v1/tasks/123'));

        $this->app->getTasksController()->expects($this->once())
            ->method('getTask')
            ->with($this->customer, '123')
            ->willReturn($task);

        $this->app->getTasksSerializer()->expects($this->once())
            ->method('serializeTask')
            ->with($task)
            ->willReturn(['some-data']);

        $this->app->getOutput()->expects($this->once())
            ->method('json')
            ->with(['some-data'], 200);

        $routes = new HttpRoutes($this->app);
        $routes->run();
    }

    public function testCreateTask(): void
    {
        $task = new Task();
        $task->id = 42;

        $this->app->method('getHeader')
            ->willReturnMap($this->getHeaders('POST', '/v1/tasks'));

        $this->app->method('getParam')
            ->willReturnMap([['title', 'Test'], ['duedate', '2020-05-22']]);

        $this->app->getTasksController()->expects($this->once())
            ->method('createTask')
            ->with($this->customer, 'Test', '2020-05-22')
            ->willReturn($task);

        $this->app->getTasksSerializer()->expects($this->once())
            ->method('serializeTask')
            ->with($task)
            ->willReturn(['some-data']);

        $this->app->getOutput()->expects($this->once())
            ->method('json')
            ->with(['some-data'], 201);

        $routes = new HttpRoutes($this->app);
        $routes->run();
    }

    public function testUpdateTask(): void
    {
        $task = new Task();
        $task->id = 123;
        $task->title = 'Test';
        $task->duedate = '2020-05-22';
        $task->completed = true;

        $this->app->method('getHeader')
            ->willReturnMap($this->getHeaders('PUT', '/v1/tasks/123'));

        $this->app->method('getParam')
            ->willReturnMap([['title', $task->title], ['duedate', $task->duedate], ['completed', (string) $task->completed]]);

        $this->app->getTasksController()->expects($this->once())
            ->method('updateTask')
            ->with($this->customer, $task);

        $this->app->getTasksSerializer()->expects($this->once())
            ->method('serializeTask')
            ->with($task)
            ->willReturn(['some-data']);

        $this->app->getOutput()->expects($this->once())
            ->method('json')
            ->with(['some-data'], 200);

        $routes = new HttpRoutes($this->app);
        $routes->run();
    }

    public function testTokenInvalidOrMissing(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('unauthorized');
        $this->expectExceptionCode(401);

        /** @var App $app */
        $app = $this->createMock(App::class);

        $routes = new HttpRoutes($app);
        $routes->run();
    }

    public function testNotFound(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('not found');
        $this->expectExceptionCode(404);

        $routes = new HttpRoutes($this->app);
        $routes->run();
    }

    protected function getHeaders(string $method, string $url): array
    {
        return [
            ['HTTP_AUTHORIZATION', 'secret'],
            ['REQUEST_METHOD', $method],
            ['DOCUMENT_URI', $url],
        ];
    }
}
