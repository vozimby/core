<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Router;

use DI\DependencyException;
use DI\NotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Vozimsan\Core\Application\App;
use Vozimsan\Core\Application\DI\Container;
use Vozimsan\Core\Application\Helpers\DirectoryHelper;
use Vozimsan\Core\Application\Http\Enums\HttpMethodEnums;
use Vozimsan\Core\Application\Router\Attributes\Method;
use Vozimsan\Core\Application\Router\DTO\RouteDTO;
use Vozimsan\Core\Application\Router\Exceptions\MethodNotAllowedException;
use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;


class Router
{
    use JsonResponseTrait;

    /**
     * @var array<string, RouteDTO>
     */
    protected static array $routes;

    /**
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(
        protected DirectoryHelper $directoryHelper,
    )
    {
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    public function init(): void
    {
        $appPath = App::$basePath.'/app/';
        $namespaces = $this->directoryHelper->getNamespaces($appPath);

        foreach ($namespaces as $namespace) {
            $this->processNamespace($namespace);
        }
    }

    /**
     * @return void
     * @throws MethodNotAllowedException
     */
    public function start(): void
    {
        $cleanRoute = App::$route;

        if (!isset(self::$routes[$cleanRoute])) {
            throw new \RuntimeException("Route $cleanRoute does not exist.");
        }

        $routeDTO = self::$routes[$cleanRoute];
        $class = $routeDTO->controller;
        $method = $routeDTO->method;
        $httpMethods = $routeDTO->httpMethods;

        $currentHttpMethod = HttpMethodEnums::from($_SERVER['REQUEST_METHOD']);

        if (count($httpMethods) > 0 && !in_array($currentHttpMethod, $httpMethods)) {
            throw new MethodNotAllowedException("Method $cleanRoute not allowed");
        }

        $response = $class->$method();

        if ($response instanceof Response) {
            $response->expire()->send();
            exit();
        }
    }

    /**
     * @param string $namespace
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    protected function processNamespace(string $namespace): void
    {
        $class = Container::getContainer()->make($namespace);
        $reflector = new \ReflectionClass($class);

        [$route, $originalRoute] = $this->fetchClassAttributes($reflector);
        [$method, $methods, $route] = $this->fetchMethodAttributes($reflector, $route);

        self::$routes[$route] = new RouteDTO($class, $method, $methods);

        foreach ($class->actions() as $action) {
            $this->processAction($action, $originalRoute);
        }
    }

    /**
     * @param \ReflectionClass $reflector
     * @return string[]
     */
    protected function fetchClassAttributes(\ReflectionClass $reflector): array
    {
        $route = '';
        $originalRoute = '';

        foreach ($reflector->getAttributes(Attributes\Route::class) as $attribute) {
            $route = $originalRoute = $attribute->newInstance()->path;
        }

        return [$route, $originalRoute];
    }

    /**
     * @param \ReflectionClass $reflector
     * @param string $route
     * @return array
     */
    protected function fetchMethodAttributes(\ReflectionClass $reflector, string $route): array
    {
        $method = '__invoke';
        $methods = [];

        foreach ($reflector->getMethods() as $refMethod) {
            foreach ($refMethod->getAttributes(Method::class) as $attribute) {
                $attribute = $attribute->newInstance();
                $route .= $attribute->path;
                $method = $refMethod->getName();
                $methods = $attribute->methods;
            }
        }

        return [$method, $methods, $route];
    }

    /**
     * @param string $action
     * @param string $originalRoute
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    protected function processAction(string $action, string $originalRoute): void
    {
        $class = Container::getContainer()->make($action);
        $reflector = new \ReflectionClass($class);
        $methods = [];

        $route = '';

        foreach ($reflector->getAttributes(Method::class) as $attribute) {
            $attributes = $attribute->newInstance();
            $route = $originalRoute . $attributes->path;
            $methods = $attributes->methods;
        }

        self::$routes[$route] = new RouteDTO($class, '__invoke', $methods);
    }

}