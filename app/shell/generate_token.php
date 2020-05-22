<?php

use TaskService\Framework\App;
use TaskService\Models\Customer;

error_reporting(E_ALL);

if (php_sapi_name() != 'cli') {
    throw new Exception('invalid interface');
}

require __DIR__ . '/../vendor/autoload.php';

$app = new App([], [], $_SERVER, []);

$customer = new Customer();
$customer->id = 42;

$token = $app->getAuthentication()->getToken($customer, $app->getConfig()->privateKey);

echo 'export TOKEN="' . $token . '"' . PHP_EOL;
