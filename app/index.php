<?php

use TaskService\Exceptions\HttpException;
use TaskService\Framework\App;

error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

$app = new App($_GET, $_POST, $_SERVER, 'php://input');

try {
    $app->getHttpRoutes()->run();
} catch (HttpException $exp) {
    $app->getOutput()->json(['error' => $exp->getMessage()], (int) $exp->getCode());
} catch (Throwable $exp) {
    // log uncaught exceptions as E_USER_WARNING
    trigger_error((string) $exp, E_USER_WARNING);

    $app->getOutput()->json(['error' => 'internal server error'], 500);
}
