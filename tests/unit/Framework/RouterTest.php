<?php

namespace TaskService\Tests\Unit\Framework;

use PHPUnit\Framework\TestCase;
use stdClass;
use TaskService\Framework\Router;

class RouterTest extends TestCase
{
    /** @var mixed */
    protected $mock;

    protected function setUp(): void
    {
        $this->mock = $this->getMockBuilder(stdClass::class)
            ->disableOriginalConstructor()
            ->addMethods(['callback', 'callbackNever'])
            ->getMock();

        $this->mock->expects($this->once())
            ->method('callBack');

        $this->mock->expects($this->never())
            ->method('callBackNever');
    }

    public function testGet(): void
    {
        $router = new Router();

        $this->mock->expects($this->once())
            ->method('callBack')
            ->with('123', 'abc');

        $router->post('/v1/api/123/abc', [$this->mock, 'callbackNever']);
        $router->get('/v1/api/other', [$this->mock, 'callbackNever']);
        $router->get('/v1/api/(\d+)/(\w+)', [$this->mock, 'callback']);

        $router->match('GET', '/v1/api/123/abc');
    }

    public function testPut(): void
    {
        $router = new Router();

        $router->post('/v1/api/abc', [$this->mock, 'callbackNever']);
        $router->put('/v1/api/other', [$this->mock, 'callbackNever']);
        $router->put('/v1/api/abc', [$this->mock, 'callback']);

        $router->match('PUT', '/v1/api/abc');
    }

    public function testPost(): void
    {
        $router = new Router();

        $router->put('/v1/api/abc', [$this->mock, 'callbackNever']);
        $router->post('/v1/api/other', [$this->mock, 'callbackNever']);
        $router->post('/v1/api/abc', [$this->mock, 'callback']);

        $router->match('POST', '/v1/api/abc');
    }

    public function testPatch(): void
    {
        $router = new Router();

        $router->post('/v1/api/abc', [$this->mock, 'callbackNever']);
        $router->patch('/v1/api/other', [$this->mock, 'callbackNever']);
        $router->patch('/v1/api/abc', [$this->mock, 'callback']);

        $router->match('PATCH', '/v1/api/abc');
    }

    public function testDelete(): void
    {
        $router = new Router();

        $router->patch('/v1/api/abc', [$this->mock, 'callbackNever']);
        $router->delete('/v1/api/other', [$this->mock, 'callbackNever']);
        $router->delete('/v1/api/abc', [$this->mock, 'callback']);

        $router->match('DELETE', '/v1/api/abc');
    }

    public function testAny(): void
    {
        $router = new Router();

        $router->any('/v1/api/test', [$this->mock, 'callbackNever']);
        $router->any('/v1/api/abc', [$this->mock, 'callback']);
        $router->any('/v1/api/abc', [$this->mock, 'callbackNever']);

        $router->match('GET', '/v1/api/abc');
    }

    public function testCli(): void
    {
        $router = new Router();

        $router->any('example\.php', [$this->mock, 'callbackNever']);
        $router->any('example\.php (\w+)', [$this->mock, 'callback']);
        $router->any('example\.php', [$this->mock, 'callbackNever']);

        $router->match('', 'example.php 123');
    }
}
