<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\DI;

use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Vozimsan\Core\Application\App;
use function DI\get;

class Container
{
    /**
     * @var \DI\Container|null
     */
    private static ?\DI\Container $container = null;

    /**
     * @return \DI\Container
     * @throws \Exception
     */
    public static function getContainer(): \DI\Container
    {
        if (is_null(self::$container)) {
            $container = new ContainerBuilder();
            $config = require App::$basePath."/config/di.php";
            $container->addDefinitions($config);

            self::$container = $container->build();
        }

        return self::$container;
    }

    /**
     * @param string $name
     * @param array $definitions
     * @return \DI\Container|mixed
     * @throws DependencyException
     * @throws NotFoundException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function getWithAllParams(string $name, array $definitions)
    {
        $reflection = new \ReflectionMethod($name, '__construct');
        $params = $reflection->getParameters();

        $defs = [];

        foreach ($params as $param) {
            $paramName = $param->getName();

            if (!$definitions[$paramName]) {
                $typeName = $param->getType()->getName();
                if (class_exists($typeName) || interface_exists($typeName)) {
                    $definitions[$paramName] = get($param->getType()->getName());
                }
            }

            $defs[$paramName] = $definitions[$paramName];
        }

        $container = self::getContainer();

        return $container->make($name, $defs);
    }
}