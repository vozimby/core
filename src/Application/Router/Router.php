<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Router;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Vozimsan\Core\Application\App;
use Vozimsan\Core\Application\Router\Loaders\AnnotationLoader;
use Doctrine\Common\Annotations\AnnotationReader;


class Router
{
    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function init(): RouteCollection
    {
        $appPath = App::$basePath.'/app';
        $directories = glob(App::$basePath . '/app/Containers/*', GLOB_ONLYDIR);

        $loader = new AnnotationDirectoryLoader(
            new FileLocator([App::$basePath]),
            new class() extends AnnotationClassLoader {
                protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot) {
                    $route->setDefault('_controller', $class->getName().'::'.$method->getName());
                }

                protected function isRouteMethod(\ReflectionMethod $method) {
                    return $method->isPublic() && !$method->isStatic() && $method->getNumberOfRequiredParameters() === 0;
                }
            }
        );

        $routeCollection = new RouteCollection();

        var_dump($directories);

        /*foreach($directories as $dir) {
            $routeCollection->addCollection($loader->load($dir));
        }*/

        return $routeCollection;
    }
}