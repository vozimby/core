<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application;

use GuzzleHttp\Client;
use Symfony\Component\Dotenv\Dotenv;
use Vozimsan\Core\Application\DI\Container;
use Vozimsan\Core\Application\Logger\FileLogger;
use Vozimsan\Core\Application\Router\Router;

class Bootstrap
{
    /**
     * @param string $rootPath
     */
    public function __construct(string $rootPath)
    {
        App::$basePath = $rootPath;
    }

    /**
     * @throws \Exception
     */
    public function init(): void
    {
        $dotEnv = new Dotenv();

        $dotEnv->loadEnv(App::$basePath."/.env");
        $container = Container::getContainer();

        $router = $container->make(Router::class);
        $router->init();
    }
}