<?php

namespace TaskService\Framework;

class Logger
{
    protected App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function log(array $event, int $code): void
    {
        if ($code >= 500 || $code < 200) {
            $status = 'ERROR';
        } elseif ($code >= 400) {
            $status = 'WARNING';
        } else {
            $status = 'INFO';
        }

        $event['status'] = $status;
        $event['timestamp'] = date('c');

        file_put_contents($this->app->getConfig()->logfile, json_encode($event) . PHP_EOL, FILE_APPEND);
    }
}
