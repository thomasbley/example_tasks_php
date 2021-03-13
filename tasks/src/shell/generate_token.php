<?php

use TaskService\Framework\App;
use TaskService\Models\Customer;

error_reporting(E_ALL);

if (php_sapi_name() != 'cli') {
    throw new Exception('invalid interface');
}

require __DIR__ . '/../vendor/autoload.php';

$app = new App([], [], $_SERVER, []);
$router = $app->getRouter();

$router->any('(\d+) (\S+)', function (int $customerId, string $email) use ($app): void {
    $customer = new Customer();
    $customer->id = $customerId;
    $customer->email = $email;

    $token = $app->getAuthentication()->getToken($customer, $app->getConfig()->privateKey);

    echo 'export TOKEN="' . $token . '"' . PHP_EOL;
});

$router->any('.*', function (): void {
    echo 'Usage: generate_token.php customer-id customer-email' . PHP_EOL;
});

$router->match('', $app->getHeader('DOCUMENT_URI'));
