<?php

namespace TaskService\Tests\Integration\Infrastructure;

use PDO;
use PHPUnit\Framework\TestCase;
use TaskService\Framework\App;

class MySqlTest extends TestCase
{
    public function testMySqlConnectionUnicode(): void
    {
        $app = new App([], [], [], []);

        $query = '
            SHOW VARIABLES WHERE Variable_name LIKE ? OR Variable_name LIKE ?
        ';
        $statement = $app->getDatabase()->prepare($query);
        $statement->execute(['character_set_%', 'collation%']);

        $variables = $statement->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $subset = [
            'character_set_client' => 'utf8mb4',
            'character_set_connection' => 'utf8mb4',
            'character_set_results' => 'utf8mb4',
            'collation_connection' => 'utf8mb4_general_ci',
        ];
        $this->assertEmpty(array_diff_assoc($subset, $variables));
    }
}
