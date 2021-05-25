<?php

namespace TaskService\Tests\Unit\Framework;

use PHPUnit\Framework\TestCase;
use TaskService\Framework\App;
use TaskService\Framework\Logger;

class LoggerTest extends TestCase
{
    public function testLog(): void
    {
        $logfile = tempnam('/tmp', 'loggertest') ?: '';

        $app = new App([], [], [], []);
        $app->getConfig()->logfile = $logfile;

        $logger = new Logger($app);
        $logger->log(['key' => 'value'], 200);

        $actual = json_decode(file_get_contents($logfile), true);

        $this->assertEquals('value', $actual['key']);
        $this->assertEquals('INFO', $actual['status']);
        $this->assertEqualsWithDelta(strtotime(date('c')), strtotime($actual['timestamp']), 5);
    }

    public function testLogWarning(): void
    {
        $logfile = tempnam('/tmp', 'loggertest') ?: '';

        $app = new App([], [], [], []);
        $app->getConfig()->logfile = $logfile;

        $logger = new Logger($app);
        $logger->log(['key' => 'value'], 404);

        $actual = json_decode(file_get_contents($logfile), true);

        $this->assertEquals('WARNING', $actual['status']);
    }

    public function testLogError(): void
    {
        $logfile = tempnam('/tmp', 'loggertest') ?: '';

        $app = new App([], [], [], []);
        $app->getConfig()->logfile = $logfile;

        $logger = new Logger($app);
        $logger->log(['key' => 'value'], 500);

        $actual = json_decode(file_get_contents($logfile), true);

        $this->assertEquals('ERROR', $actual['status']);
    }
}
