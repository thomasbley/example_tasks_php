<?php

namespace TaskService\Test\Integration\Infrastructure;

use PHPUnit\Framework\TestCase;
use TaskService\Framework\App;

class MemcacheTest extends TestCase
{
    public function testMemcacheConnection(): void
    {
        $app = new App([], [], [], []);

        $cache = $app->getMemcache();

        $cache->set('foo', 'bar', 0, 1);

        $this->assertSame('bar', $cache->get('foo'));

        $actual = $cache->getstats();

        $this->assertNotEmpty($actual);
        $this->assertNotEmpty($actual['version']);
    }
}
