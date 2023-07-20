<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\ServiceProviders;

use DI\DependencyException;
use DI\NotFoundException;
use Rakit\Validation\Validator;
use Vozimsan\Core\Application\App;
use Vozimsan\Core\Application\DI\Container;
use Vozimsan\Core\Application\Middlewares\MiddlewareInterface;
use Vozimsan\Core\Rest\Http\Traits\RequestTrait;
use function DI\get;

final class ServiceProvider
{
    use RequestTrait;
    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws \Exception
     */
    public function init(): void
    {
        $providers = require App::$basePath.'/config/providers.php';
        $this->setRequest();

        foreach ($providers as $provider) {
            /** @var AbstractServiceProvider $providerClass */
            $providerClass = Container::getContainer()
                ->make($provider);

            $providerClass->boot();

            foreach ($providerClass->middlewares as $middleware) {
                $middlewareClass = Container::getContainer()
                    ->make($middleware);

                if ($middlewareClass instanceof MiddlewareInterface) {
                    $middlewareResponse = $middlewareClass->process($this->request);

                    if ($middlewareResponse) {
                        $middlewareResponse->expire()->send();
                        exit();
                    }
                }
            }
        }
    }
}