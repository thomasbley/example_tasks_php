<?php

namespace TaskService\Config;

/**
 * application config
 */
class Config
{
    public $dbHost = 'mysql';
    public $dbUsername = 'root';
    public $dbPassword = 'root';
    public $dbDatabase = 'tasks';

    public $cacheHosts = ['memcache'];
}
