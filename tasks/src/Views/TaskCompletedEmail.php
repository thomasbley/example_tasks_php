<?php

namespace TaskService\Views;

use TaskService\Models\Customer;
use TaskService\Models\Email;
use TaskService\Models\Task;

class TaskCompletedEmail extends Email
{
    public string $template = __DIR__ . '/TaskCompletedEmailTemplate.php';

    public Customer $customer;

    public Task $task;
}
