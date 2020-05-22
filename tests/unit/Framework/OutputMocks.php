<?php

namespace TaskService\Framework;

function header(string $header): void
{
    OutputMocks::$header[] = $header;
}

abstract class OutputMocks
{
    public static $header = [];
}
