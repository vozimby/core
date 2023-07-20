<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application;

use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Dotenv\Dotenv;
use Throwable;
use Vozimsan\Core\Application\DI\Container;
use Vozimsan\Core\Application\Http\Constants\StatusCode;
use Vozimsan\Core\Application\Logger\FileLogger;
use Vozimsan\Core\Application\Router\Exceptions\MethodNotAllowedException;
use Vozimsan\Core\Application\Router\Router;
use Vozimsan\Core\Application\ServiceProviders\ServiceProvider;
use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;

class Bootstrap
{
    use JsonResponseTrait;

    /**
     * @param string $rootPath
     */
    public function __construct(string $rootPath)
    {
        App::$basePath = $rootPath;
        App::$route = explode("?", $_SERVER['REQUEST_URI'])[0];
    }

    /**
     * @throws Exception
     */
    public function init(): void
    {
        $dotEnv = new Dotenv();

        $dotEnv->loadEnv(App::$basePath . "/.env");
        $container = Container::getContainer();

        try {
            $serviceProvider = $container->make(ServiceProvider::class);
            $serviceProvider->init();

            $router = $container->make(Router::class);
            $router->init();
            $router->start();
        } catch (\RuntimeException $exception) {
            $this->error($exception->getMessage(), StatusCode::NOT_FOUND)->expire()->send();
            exit();
        } catch (MethodNotAllowedException $exception) {
            $this->error($exception->getMessage(), StatusCode::METHOD_NOT_ALLOWED)->expire()->send();
            exit();
        } catch (Exception|Throwable $exception) {
            $this->error($exception->getMessage(), StatusCode::SERVER_ERROR)->expire()->send();
            exit();
        }
    }
}