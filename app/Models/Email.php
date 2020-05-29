<?php

namespace TaskService\Models;

class Email
{
    /** @var string */
    public $template;

    /** @var string */
    public $subject;

    /** @var string */
    public $from;

    /** @var Customer */
    public $customer;
}
