<?php

namespace TaskService\Tests\Unit\Framework;

use PHPUnit\Framework\TestCase;
use TaskService\Framework\Router;

class RouterTest extends TestCase
{
    public function testGet(): void
    {
        $with = [];
        $never = false;
        $called = false;

        $router = new Router();

        $router->post('/v1/api/123/abc', function() use (&$never): void { $never = true; });
        $router->get('/v1/api/other', function() use (&$never): void { $never = true; });
        $router->get('/v1/api/(\d+)/(\w+)', function(int $i, string $s) use (&$called, &$with): void {
            $called = true; $with = [$i, $s];
        });

        $router->match('GET', '/v1/api/123/abc');

        $this->assertFalse($never);
        $this->assertTrue($called);
        $this->assertEquals([123, 'abc'], $with);
    }

    public function testPut(): void
    {
        $never = false;
        $called = false;

        $router = new Router();

        $router->post('/v1/api/abc', function() use (&$never): void { $never = true; });
        $router->put('/v1/api/other', function() use (&$never): void { $never = true; });
        $router->put('/v1/api/abc', function() use (&$called): void { $called = true; });

        $router->match('PUT', '/v1/api/abc');

        $this->assertFalse($never);
        $this->assertTrue($called);
    }

    public function testPost(): void
    {
        $never = false;
        $called = false;

        $router = new Router();

        $router->put('/v1/api/abc', function() use (&$never): void { $never = true; });
        $router->post('/v1/api/other', function() use (&$never): void { $never = true; });
        $router->post('/v1/api/abc', function() use (&$called): void { $called = true; });

        $router->match('POST', '/v1/api/abc');

        $this->assertFalse($never);
        $this->assertTrue($called);
    }

    public function testPatch(): void
    {
        $never = false;
        $called = false;

        $router = new Router();

        $router->post('/v1/api/abc', function() use (&$never): void { $never = true; });
        $router->patch('/v1/api/other', function() use (&$never): void { $never = true; });
        $router->patch('/v1/api/abc', function() use (&$called): void { $called = true; });

        $router->match('PATCH', '/v1/api/abc');

        $this->assertFalse($never);
        $this->assertTrue($called);
    }

    public function testDelete(): void
    {
        $never = false;
        $called = false;

        $router = new Router();

        $router->patch('/v1/api/abc', function() use (&$never): void { $never = true; });
        $router->delete('/v1/api/other', function() use (&$never): void { $never = true; });
        $router->delete('/v1/api/abc', function() use (&$called): void { $called = true; });

        $router->match('DELETE', '/v1/api/abc');

        $this->assertFalse($never);
        $this->assertTrue($called);
    }

    public function testAny(): void
    {
        $never = false;
        $called = false;

        $router = new Router();

        $router->any('/v1/api/test', function() use (&$never): void { $never = true; });
        $router->any('/v1/api/abc', function() use (&$called): void { $called = true; });
        $router->any('/v1/api/abc', function() use (&$never): void { $never = true; });

        $router->match('GET', '/v1/api/abc');

        $this->assertFalse($never);
        $this->assertTrue($called);
    }

    public function testCli(): void
    {
        $with = '';
        $never = false;
        $called = false;

        $router = new Router();

        $router->any('example\.php', function() use (&$never): void { $never = true; });
        $router->any('example\.php (\w+)', function(string $s) use (&$called, &$with): void {
            $called = true; $with = $s;
        });
        $router->any('example\.php', function() use (&$never): void { $never = true; });

        $router->match('', 'example.php 123');

        $this->assertFalse($never);
        $this->assertTrue($called);
        $this->assertEquals('123', $with);
    }
}
