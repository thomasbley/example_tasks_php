<?php

namespace TaskService\Services;

/**
 * @param array ...$params
 */
function mail(...$params): bool
{
    ServicesMocks::$mailParams = $params;

    return ServicesMocks::$mailReturn;
}

abstract class ServicesMocks
{
    public static bool $mailReturn = true;

    public static array $mailParams = [];
}
