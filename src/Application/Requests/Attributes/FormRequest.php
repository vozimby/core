<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Requests\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS| Attribute::TARGET_METHOD)]
class FormRequest
{
    /**
     * @param string $formRequest
     */
    public function __construct(
        public string $formRequest
    )
    {
    }
}