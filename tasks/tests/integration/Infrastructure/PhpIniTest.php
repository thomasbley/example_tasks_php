<?php

namespace TaskService\Tests\Integration\Infrastructure;

use PHPUnit\Framework\TestCase;

class PhpIniTest extends TestCase
{
    public function testExtensions(): void
    {
        $requiredExtensions = [
            'curl', 'filter', 'hash', 'json', 'mbstring', 'openssl', 'pcre',
            'pdo_mysql', 'SPL', 'zlib', 'Zend OPcache',
        ];

        $extensions = get_loaded_extensions();

        $diff = array_diff($requiredExtensions, $extensions);

        $this->assertEmpty($diff, 'missing extensions: ' . json_encode($diff));
    }
}
