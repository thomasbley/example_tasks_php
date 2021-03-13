<?php

namespace TaskService\Framework;

class Router
{
    protected array $routes = [];

    public function get(string $pattern, callable $callback): void
    {
        $this->routes[] = ['GET', $pattern, $callback];
    }

    public function put(string $pattern, callable $callback): void
    {
        $this->routes[] = ['PUT', $pattern, $callback];
    }

    public function post(string $pattern, callable $callback): void
    {
        $this->routes[] = ['POST', $pattern, $callback];
    }

    public function patch(string $pattern, callable $callback): void
    {
        $this->routes[] = ['PATCH', $pattern, $callback];
    }

    public function delete(string $pattern, callable $callback): void
    {
        $this->routes[] = ['DELETE', $pattern, $callback];
    }

    public function any(string $pattern, callable $callback): void
    {
        $this->routes[] = ['', $pattern, $callback];
    }

    public function match(string $requestMethod, string $requestPath): void
    {
        foreach ($this->routes as $route) {
            list($method, $pattern, $callback) = $route;

            if ($method != '' && $requestMethod != $method) {
                continue;
            }

            // extract parameter values from URL
            $values = [];
            if (!preg_match('#^' . $pattern . '$#', $requestPath, $values)) {
                continue;
            }
            array_shift($values);

            call_user_func_array($callback, $values);

            break;
        }
    }
}
