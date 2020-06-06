<?php

namespace TaskService\Models;

class Email
{
    public string $template;

    public string $subject;

    public string $from;

    public Customer $customer;
}
