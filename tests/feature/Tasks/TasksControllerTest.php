<?php

namespace TaskService\Tests\Feature\Tasks;

use PHPUnit\Framework\TestCase;
use TaskService\Config\Config;
use TaskService\Framework\Authentication;
use TaskService\Models\Customer;

class TasksTest extends TestCase
{
    protected string $baseUrl = 'http://nginx:8080/';

    protected string $authorization;

    protected Customer $customer;

    public function setUp(): void
    {
        $config = new Config();
        $authentication = new Authentication();

        $this->customer = new Customer();
        $this->customer->id = 42;
        $this->customer->email = 'foo.bar@example.com';

        $this->authorization = $authentication->getToken($this->customer, $config->privateKey);
    }

    public function testUrlNotFound(): void
    {
        $actual = $this->doRequest('GET', '/foobar', [], 404);

        $this->assertNotEmpty($actual);
        $this->assertStringContainsString('404 Not Found', $actual[0]);
    }

    public function testTaskNotFound(): void
    {
        $actual = $this->doRequest('GET', '/v1/tasks/123123', [], 404);

        $this->assertEquals(['error' => 'task not found'], $actual);
    }

    public function testGetTask(): void
    {
        $expected = $this->createTask();

        $actual = $this->doRequest('GET', '/v1/tasks/' . $expected['id'], [], 200);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateTask(): void
    {
        $task = $this->doRequest('POST', '/v1/tasks', ['title' => 'test', 'duedate' => '2020-05-22'], 201);

        $expected = ['id' => $task['id'], 'title' => 'test', 'duedate' => '2020-05-22', 'completed' => false];

        $actual = $this->getTask($task['id']);

        $this->assertEquals($expected, $task);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateTask(): void
    {
        $task = $this->createTask();

        $expected = ['id' => $task['id'], 'title' => 'test2', 'duedate' => '2020-06-22', 'completed' => true];

        $task = $this->doRequest('PUT', '/v1/tasks/' . $task['id'], $expected, 200);

        $actual = $this->getTask($task['id']);

        $this->assertEquals($expected, $task);
        $this->assertEquals($expected, $actual);
    }

    public function testDeleteTask(): void
    {
        $task = $this->createTask();

        $actual = $this->doRequest('DELETE', '/v1/tasks/' . $task['id'], [], 204);

        $task = $this->doRequest('GET', '/v1/tasks/' . $task['id'], [], 404);

        $this->assertEmpty($actual[0]);
        $this->assertEquals(['error' => 'task not found'], $task);
    }

    public function testGetCurrentTasks(): void
    {
        $task = $this->createTask();

        $actual = $this->doRequest('GET', '/v1/tasks', [], 200);

        $this->assertContains($task, $actual);
    }

    public function testGetCompletedTasks(): void
    {
        $task = $this->createTask();

        $data = ['id' => $task['id'], 'title' => 'test2', 'duedate' => '2020-06-22', 'completed' => true];
        $this->doRequest('PUT', '/v1/tasks/' . $task['id'], $data, 200);

        $actual = $this->doRequest('GET', '/v1/tasks', ['completed' => 1], 200);

        $this->assertContains($data, $actual);
    }

    protected function createTask(): array
    {
        return $this->doRequest('POST', '/v1/tasks', ['title' => 'test', 'duedate' => '2020-05-22'], 201);
    }

    protected function getTask(int $taskId): array
    {
        return $this->doRequest('GET', '/v1/tasks/' . $taskId, [], 200);
    }

    protected function doRequest(string $method, string $url, array $params, int $code): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_URL => $this->baseUrl . $url,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: ' . $this->authorization],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 5,
        ]);

        $response = (string) curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->assertEquals($code, $responseCode);

        curl_close($ch);

        return json_decode($response, true) ?: [$response];
    }
}
