<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\ServiceProviders;

abstract class AbstractServiceProvider
{
    /**
     * @var array
     */
    public array $middlewares = [];

    /**
     * @return void
     */
    abstract public function boot(): void;

    /**
     * @param array $middlewares
     */
    public function setMiddlewares(array $middlewares): void
    {
        $this->middlewares = $middlewares;
    }
}