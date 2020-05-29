<?php

namespace TaskService\Controllers;

use DateTime;
use TaskService\Exceptions\HttpException;
use TaskService\Framework\App;
use TaskService\Models\Customer;
use TaskService\Models\Task;
use TaskService\Views\TaskCompletedEmail;

class TasksController
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function createTask(Customer $customer, string $title, string $duedate): Task
    {
        if (empty($title)) {
            throw new HttpException('missing title', 400);
        }
        if (!DateTime::createFromFormat('Y-m-d', $duedate)) {
            throw new HttpException('invalid duedate', 400);
        }

        $repo = $this->app->getTasksRepository();

        return $repo->createTask($customer, $title, $duedate);
    }

    public function updateTask(Customer $customer, Task $task): void
    {
        if (empty($task->title)) {
            throw new HttpException('missing title', 400);
        }
        if (!DateTime::createFromFormat('Y-m-d', $task->duedate)) {
            throw new HttpException('invalid duedate', 400);
        }

        $repo = $this->app->getTasksRepository();

        if (!$repo->taskExists($customer, $task->id)) {
            throw new HttpException('task not found', 404);
        }

        $repo->updateTask($task);

        if ($task->completed) {
            $email = new TaskCompletedEmail();
            $email->customer = $customer;
            $email->task = $task;

            $this->app->getEmailService()->sendEmail($email);
        }
    }

    public function deleteTask(Customer $customer, int $taskId): void
    {
        $repo = $this->app->getTasksRepository();

        if (!$repo->taskExists($customer, $taskId)) {
            throw new HttpException('task not found', 404);
        }

        $repo->deleteTask($taskId);
    }

    /**
     * @return Task[]
     */
    public function getCurrentTasks(Customer $customer): array
    {
        $repo = $this->app->getTasksRepository();

        return $repo->getCurrentTasks($customer);
    }

    /**
     * @return Task[]
     */
    public function getCompletedTasks(Customer $customer): array
    {
        $repo = $this->app->getTasksRepository();

        return $repo->getCompletedTasks($customer);
    }

    public function getTask(Customer $customer, int $taskId): Task
    {
        $repo = $this->app->getTasksRepository();

        $task = $repo->getTask($customer, $taskId);

        if (empty($task)) {
            throw new HttpException('task not found', 404);
        }

        return $task;
    }
}
