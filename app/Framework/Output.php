<?php

namespace TaskService\Framework;

class Output
{
    public function json(array $data, int $code, string $location = ''): void
    {
        http_response_code($code);
        header('Content-Type: application/json');

        if ($location !== '') {
            header('Location: ' . $location);
        }

        echo json_encode($data);
    }

    public function noContent(): void
    {
        http_response_code(204);
    }
}
