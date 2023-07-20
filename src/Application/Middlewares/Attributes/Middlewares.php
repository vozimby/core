<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Middlewares\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class Middlewares
{
    /**
     * @param string[] $middlewares
     */
    public function __construct(
        public array $middlewares,
    )
    {
    }
}