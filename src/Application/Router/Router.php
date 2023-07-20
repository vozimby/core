<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Router;

use DI\DependencyException;
use DI\NotFoundException;
use Rakit\Validation\Validator;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;
use Vozimsan\Core\Application\Actions\AbstractBaseAction;
use Vozimsan\Core\Application\App;
use Vozimsan\Core\Application\Controllers\AbstractBaseController;
use Vozimsan\Core\Application\DI\Container;
use Vozimsan\Core\Application\Helpers\DirectoryHelper;
use Vozimsan\Core\Application\Http\Enums\HttpMethodEnums;
use Vozimsan\Core\Application\Middlewares\Attributes\Middlewares;
use Vozimsan\Core\Application\Middlewares\MiddlewareInterface;
use Vozimsan\Core\Application\Requests\AbstractFormRequest;
use Vozimsan\Core\Application\Requests\Attributes\FormRequest;
use Vozimsan\Core\Application\Router\Attributes\Method;
use Vozimsan\Core\Application\Router\DTO\RouteDTO;
use Vozimsan\Core\Application\Router\Exceptions\MethodNotAllowedException;
use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;
use function DI\autowire;
use function DI\get;


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
     * @throws \Exception
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

        $reflector = new ReflectionClass($class);

        $this->processMiddlewares($reflector, $class);
        $this->processFormRequest($reflector, $class);

        foreach ($reflector->getMethods() as $classMethod) {
            $this->processMiddlewares($classMethod, $class);
            $this->processFormRequest($classMethod, $class);
        }

        $response = Container::getContainer()
            ->call([$class, $method]);

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
        $reflector = new ReflectionClass($class);

        [$route, $originalRoute] = $this->fetchClassAttributes($reflector);
        [$method, $methods, $route] = $this->fetchMethodAttributes($reflector, $route);

        self::$routes[$route] = new RouteDTO($class, $method, $methods);

        foreach ($class->actions() as $action) {
            $this->processAction($action, $originalRoute);
        }
    }

    /**
     * @param ReflectionClass $reflector
     * @return string[]
     */
    protected function fetchClassAttributes(ReflectionClass $reflector): array
    {
        $route = '';
        $originalRoute = '';

        foreach ($reflector->getAttributes(Attributes\Route::class) as $attribute) {
            $route = $originalRoute = $attribute->newInstance()->path;
        }

        return [$route, $originalRoute];
    }

    /**
     * @param ReflectionClass $reflector
     * @param string $route
     * @return array
     */
    protected function fetchMethodAttributes(ReflectionClass $reflector, string $route): array
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
        $reflector = new ReflectionClass($class);
        $methods = [];

        $route = '';

        foreach ($reflector->getAttributes(Method::class) as $attribute) {
            $attributes = $attribute->newInstance();
            $route = $originalRoute . $attributes->path;
            $methods = $attributes->methods;
        }

        self::$routes[$route] = new RouteDTO($class, '__invoke', $methods);
    }

    /**
     * @param ReflectionClass|ReflectionMethod $reflector
     * @param AbstractBaseController|AbstractBaseAction $class
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function processMiddlewares(ReflectionClass|ReflectionMethod $reflector, AbstractBaseController|AbstractBaseAction $class): void
    {
        foreach ($reflector->getAttributes(Middlewares::class) as $attribute) {
            $instance = $attribute->newInstance();
            foreach ($instance->middlewares as $middleware) {
                /** @var MiddlewareInterface $middlewareClass */
                $middlewareClass = Container::getContainer()
                    ->make($middleware);

                if ($middlewareClass instanceof MiddlewareInterface) {
                    $middlewareResponse = $middlewareClass->process($class->request);

                    if ($middlewareResponse) {
                        $middlewareResponse->expire()->send();
                        exit();
                    }
                }
            }
        }
    }

    /**
     * @param ReflectionClass|ReflectionMethod $reflector
     * @param AbstractBaseController|AbstractBaseAction $class
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    protected function processFormRequest(ReflectionClass|ReflectionMethod $reflector, AbstractBaseController|AbstractBaseAction $class): void
    {
        foreach ($reflector->getAttributes(FormRequest::class) as $attribute) {
            $instance = $attribute->newInstance();

            $formRequestClass = Container::getWithAllParams($instance->formRequest, [
                'request' => $class->request,
                'validator' => get(Validator::class),
            ]);

            if ($formRequestClass instanceof AbstractFormRequest) {
                $response = $formRequestClass->validate();

                if ($response instanceof Response) {
                    $response->expire()->send();
                    exit();
                }
            }
        }
    }
}