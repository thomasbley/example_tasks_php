<?php

namespace TaskService\Test\Unit\Framework;

use Exception;
use PHPUnit\Framework\TestCase;
use TaskService\Config\Config;
use TaskService\Framework\Authentication;
use TaskService\Models\Customer;

class AuthenticationTest extends TestCase
{
    public function testGetToken(): void
    {
        $config = new Config();

        $customer = new Customer();
        $customer->id = 42;

        $authentication = new Authentication();

        $token = $authentication->getToken($customer, $config->privateKey);

        list($header, $payload) = explode('.', substr($token, 7));

        $actual = json_decode(base64_decode(strtr($payload, '-_', '+/')), true, 10);

        $this->assertNotEmpty($actual);
        $this->assertLessThan(time() + 23 * 7200, $actual['exp']);
        $this->assertSame('42', $actual['sub']);

        $actual = json_decode(base64_decode(strtr($header, '-_', '+/')), true, 10);

        $this->assertNotEmpty($actual);
        $this->assertSame('JWT', $actual['typ']);
        $this->assertSame('RS512', $actual['alg']);
    }

    public function testGetCustomer(): void
    {
        $config = new Config();

        $customer = new Customer();
        $customer->id = 42;

        $authentication = new Authentication();

        $token = $authentication->getToken($customer, $config->privateKey);

        $actual = $authentication->getCustomer($token, $config->publicKey);

        $this->assertNotEmpty($actual);
        $this->assertSame(42, $actual->id);
    }

    public function testGetCustomerInvalidToken(): void
    {
        $config = new Config();
        $customer = new Customer();
        $authentication = new Authentication();

        $actual = $authentication->getCustomer('foo', $config->publicKey);
        $this->assertNull($actual);

        $actual = $authentication->getCustomer('Bearer foo.bar.baz', $config->publicKey);
        $this->assertNull($actual);

        $token = $authentication->getToken($customer, $config->privateKey);

        $actual = $authentication->getCustomer($token, $config->publicKey);
        $this->assertNull($actual);
    }

    public function testGetCustomerInvalidKey(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('signing failed');

        $customer = new Customer();
        $authentication = new Authentication();

        @$authentication->getToken($customer, '');
    }
}
