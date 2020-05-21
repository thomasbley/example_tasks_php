<?php

namespace TaskService\Repositories;

use InvalidArgumentException;
use TaskService\Framework\App;

class MigrationsRepository
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function importSqlFile(string $file): void
    {
        if (!is_readable($file)) {
            throw new InvalidArgumentException('invalid file');
        }

        $db = $this->app->getDatabase();

        $db->exec(file_get_contents($file));

        $query = 'INSERT INTO migration SET filename = ?, created_at = now()';
        $db->prepare($query)->execute([basename($file)]);
    }

    public function isImported(string $filename): bool
    {
        $db = $this->app->getDatabase();

        $query = "SHOW tables LIKE 'migration'";
        $result = $db->query($query)->fetch();
        if (empty($result)) {
            return false;
        }

        $query = 'SELECT filename FROM migration WHERE filename = ?';
        $statement = $db->prepare($query);
        $statement->execute([$filename]);

        return $statement->rowCount() ? true : false;
    }
}
