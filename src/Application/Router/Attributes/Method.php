<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Router\Attributes;

use Attribute;
use Vozimsan\Core\Application\Http\Enums\HttpMethodEnums;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Method
{
    /**
     * @param string $path
     * @param HttpMethodEnums[] $methods
     */
    public function __construct(
        public string $path,
        public array $methods
    )
    {
    }
}