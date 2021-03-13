<?php

namespace TaskService\Tests\Unit\Framework;

use PHPUnit\Framework\TestCase;
use TaskService\Framework\Output;
use TaskService\Framework\OutputMocks;

class OutputTest extends TestCase
{
    public function setUp(): void
    {
        require_once __DIR__ . '/OutputMocks.php';

        OutputMocks::$header = [];
    }

    public function testJson(): void
    {
        $this->expectOutputString('{"param":"value"}');

        $output = new Output();
        $output->json(['param' => 'value'], 200);

        $this->assertSame(200, http_response_code());
        $this->assertSame(['Content-Type: application/json'], OutputMocks::$header);
    }

    public function testJsonLocation(): void
    {
        $this->expectOutputString('{"foo":"bar"}');

        $output = new Output();
        $output->json(['foo' => 'bar'], 200, '/foobar');

        $this->assertSame(200, http_response_code());
        $this->assertSame(['Content-Type: application/json', 'Location: /foobar'], OutputMocks::$header);
    }

    public function testNoContent(): void
    {
        $this->expectOutputString('');

        $output = new Output();
        $output->noContent();

        $this->assertSame(204, http_response_code());
        $this->assertSame([], OutputMocks::$header);
    }
}
