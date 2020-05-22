<?php

namespace TaskService\Test\Integration\Repositories;

use InvalidArgumentException;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use TaskService\Framework\App;
use TaskService\Repositories\MigrationsRepository;

class MigrationsRepositoryTest extends TestCase
{
    protected $app;

    public function setUp(): void
    {
        $this->app = new App([], [], $_SERVER, []);

        $this->app->getDatabase()->beginTransaction();

        $this->createSqlFile();
    }

    public function tearDown(): void
    {
        $this->app->getDatabase()->rollBack();

        unlink('/tmp/migration.sql');
    }

    public function testImportSqlFile(): void
    {
        $repo = $this->app->getMigrationsRepository();

        $repo->importSqlFile('/tmp/migration.sql');

        $db = $this->app->getDatabase();

        $query = 'SELECT filename FROM migration WHERE filename = "migration.sql"';
        $this->assertNotEmpty($db->query($query)->fetchColumn());

        $query = 'SELECT id FROM task WHERE customer_id = 42 and title = "test"';
        $this->assertNotEmpty($db->query($query)->fetchColumn());
    }

    public function testImportSqlFileUnknown(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $repo = $this->app->getMigrationsRepository();

        $repo->importSqlFile('/tmp/unknown.sql');
    }

    public function testIsImported(): void
    {
        $repo = $this->app->getMigrationsRepository();

        $this->assertFalse($repo->isImported('migration.sql'));

        $repo->importSqlFile('/tmp/migration.sql');

        $this->assertTrue($repo->isImported('migration.sql'));
        $this->assertFalse($repo->isImported('unknown.sql'));
    }

    public function testIsImportedNoSchema(): void
    {
        $map = ['getDatabase' => $this->createMock(PDO::class)];
        $app = $this->createConfiguredMock(App::class, $map);

        $app->getDatabase()->expects($this->once())
            ->method('query')
            ->willReturn(new PDOStatement());

        $repo = new MigrationsRepository($app);
        $this->assertFalse($repo->isImported('imported.sql'));
    }

    protected function createSqlFile(): void
    {
        $query = 'INSERT INTO task SET customer_id = 42, title = "test", duedate = "2020-05-22", completed = 0;';
        file_put_contents('/tmp/migration.sql', $query);

        $this->assertFileIsReadable('/tmp/migration.sql');
    }
}
