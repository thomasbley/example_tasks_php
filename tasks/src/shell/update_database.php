<?php

use TaskService\Framework\App;

error_reporting(E_ALL);

if (php_sapi_name() != 'cli') {
    throw new Exception('invalid interface');
}

require __DIR__ . '/../vendor/autoload.php';

$path = __DIR__ . '/../Migrations/';

$app = new App([], [], $_SERVER, []);
$repo = $app->getMigrationsRepository();

foreach (scandir($path) ?: [] as $file) {
    if (strpos($file, '.sql') === false || $repo->isImported($file)) {
        continue;
    }

    echo 'Processing ' . $file . PHP_EOL;

    $repo->importSqlFile($path . $file);
}
