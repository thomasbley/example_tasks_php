<?php

namespace TaskService\Models;

class Task
{
    /** @var int */
    public $id;

    /** @var string */
    public $title;

    /** @var string */
    public $duedate;

    /** @var bool */
    public $completed;
}
