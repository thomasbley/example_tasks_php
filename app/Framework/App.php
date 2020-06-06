<?php

namespace TaskService\Framework;

use Memcache;
use PDO;
use TaskService\Config\Config;
use TaskService\Controllers\TasksController;
use TaskService\Repositories\MigrationsRepository;
use TaskService\Repositories\TasksRepository;
use TaskService\Routes\HttpRoutes;
use TaskService\Serializer\TasksSerializer;
use TaskService\Services\EmailService;

class App
{
    protected array $get;
    protected array $post;
    protected array $server;
    protected array $input;
    protected array $container = [];

    /**
     * @param array|string $input
     */
    public function __construct(array $get, array $post, array $server, $input)
    {
        // trim request data
        $this->get = filter_var($get, FILTER_CALLBACK, ['options' => 'trim']);
        $this->post = filter_var($post, FILTER_CALLBACK, ['options' => 'trim']);

        // fill path_info with argv if given
        if (empty($server['DOCUMENT_URI']) && !empty($server['argv'])) {
            $server['DOCUMENT_URI'] = implode(' ', array_slice($server['argv'], 1));
        }
        $this->server = $server;

        if (!is_array($input)) {
            $input = (array) json_decode((string) file_get_contents($input), true, 10);
        }
        $this->input = filter_var($input, FILTER_CALLBACK, ['options' => 'trim']);
    }

    /**
     * @return mixed
     */
    public function getParam(string $key)
    {
        return $this->input[$key] ?? $this->post[$key] ?? $this->get[$key] ?? '';
    }

    public function getHeader(string $key): string
    {
        return $this->server[$key] ?? '';
    }

    public function getConfig(): Config
    {
        if (empty($this->container['config'])) {
            $this->container['config'] = new Config();
        }

        return $this->container['config'];
    }

    public function getDatabase(): PDO
    {
        if (!isset($this->container['database'])) {
            $config = $this->getConfig();

            $dsn = sprintf('mysql:host=%s;dbname=%s;port=3306;charset=utf8mb4;', $config->dbHost, $config->dbDatabase);

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->container['database'] = new PDO($dsn, $config->dbUsername, $config->dbPassword, $options);
        }

        return $this->container['database'];
    }

    public function getMemcache(): Memcache
    {
        if (!isset($this->container['memcache'])) {
            $config = $this->getConfig();

            $cache = new Memcache();
            foreach ($config->cacheHosts as $host) {
                $cache->addserver($host);
            }

            $this->container['memcache'] = $cache;
        }

        return $this->container['memcache'];
    }

    public function getMigrationsRepository(): MigrationsRepository
    {
        return new MigrationsRepository($this);
    }

    public function getRouter(): Router
    {
        return new Router();
    }

    public function getHttpRoutes(): HttpRoutes
    {
        return new HttpRoutes($this);
    }

    public function getOutput(): Output
    {
        return new Output();
    }

    public function getAuthentication(): Authentication
    {
        return new Authentication();
    }

    public function getEmailService(): EmailService
    {
        return new EmailService();
    }

    public function getTasksController(): TasksController
    {
        return new TasksController($this);
    }

    public function getTasksRepository(): TasksRepository
    {
        return new TasksRepository($this);
    }

    public function getTasksSerializer(): TasksSerializer
    {
        return new TasksSerializer();
    }
}
