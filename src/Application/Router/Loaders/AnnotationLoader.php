<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Router\Loaders;

use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;

class AnnotationLoader extends AnnotationClassLoader
{

    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param object $annot
     * @return void
     */
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, object $annot): void
    {
        var_dump($route, $class, $method);
    }
}