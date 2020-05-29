<?php

namespace TaskService\Views;

use TaskService\Models\Customer;
use TaskService\Models\Email;
use TaskService\Models\Task;

class TaskCompletedEmail extends Email
{
    public $template = __DIR__ . '/TaskCompletedEmailTemplate.php';

    /** @var Customer */
    public $customer;

    /** @var Task */
    public $task;
}
