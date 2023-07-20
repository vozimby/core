<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Router\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Route
{
    /**
     * @param string $path
     * @param string $name
     */
    public function __construct(
        public string $path,
        public string $name = '',
    )
    {
    }
}