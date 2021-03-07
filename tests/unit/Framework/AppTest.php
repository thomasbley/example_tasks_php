<?php

namespace TaskService\Tests\Unit\Framework;

use PHPUnit\Framework\TestCase;
use TaskService\Config\Config;
use TaskService\Controllers\TasksController;
use TaskService\Framework\App;
use TaskService\Framework\Authentication;
use TaskService\Framework\Logger;
use TaskService\Framework\Output;
use TaskService\Framework\Router;
use TaskService\Repositories\MigrationsRepository;
use TaskService\Repositories\TasksRepository;
use TaskService\Routes\HttpRoutes;
use TaskService\Serializer\TasksSerializer;
use TaskService\Services\EmailService;

class AppTest extends TestCase
{
    public function testGetParam(): void
    {
        $get = ['param' => 'value', 'param2' => 'ignored'];
        $post = ['param2' => 'value2', 'param3' => 'ignored'];
        $input = ['param3' => 'value3'];
        $app = new App($get, $post, [], $input);

        $this->assertSame('value', $app->getParam('param'));
        $this->assertSame('value2', $app->getParam('param2'));
        $this->assertSame('value3', $app->getParam('param3'));
        $this->assertSame('', $app->getParam('invalid'));

        file_put_contents('/tmp/test.json', json_encode(['param' => 'value1']));

        $app = new App([], [], [], '/tmp/test.json');
        $this->assertSame('value1', $app->getParam('param'));

        $app = new App(['param' => ' value '], ['param2' => ' value2 '], [], ['param3' => ' value3 ']);
        $this->assertSame('value', $app->getParam('param'));
        $this->assertSame('value2', $app->getParam('param2'));
        $this->assertSame('value3', $app->getParam('param3'));
    }

    public function testGetHeader(): void
    {
        $app = new App([], [], ['header' => 'value'], []);
        $this->assertSame('value', $app->getHeader('header'));
        $this->assertSame('', $app->getHeader('invalid'));

        $app = new App([], [], ['argv' => ['test.php', 'param', 'value']], []);
        $this->assertSame('param value', $app->getHeader('DOCUMENT_URI'));
    }

    public function testGetRouter(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(Router::class, $app->getRouter());
    }

    public function testGetLogger(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(Logger::class, $app->getLogger());
    }

    public function testGetHttproutes(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(HttpRoutes::class, $app->getHttpRoutes());
    }

    public function testGetOutput(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(Output::class, $app->getOutput());
    }

    public function testGetAuthentication(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(Authentication::class, $app->getAuthentication());
    }

    public function testGetConfig(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(Config::class, $app->getConfig());
    }

    public function testGetEmailService(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(EmailService::class, $app->getEmailService());
    }

    public function testGetTasksController(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(TasksController::class, $app->getTasksController());
    }

    public function testGetMigrationsRepository(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(MigrationsRepository::class, $app->getMigrationsRepository());
    }

    public function testGetTasksRepository(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(TasksRepository::class, $app->getTasksRepository());
    }

    public function testGetTasksSerializer(): void
    {
        $app = new App([], [], [], []);
        $this->assertInstanceOf(TasksSerializer::class, $app->getTasksSerializer());
    }
}
