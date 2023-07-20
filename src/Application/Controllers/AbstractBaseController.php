<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Controllers;

use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;
use Vozimsan\Core\Rest\Http\Traits\RequestTrait;

abstract class AbstractBaseController
{
    use JsonResponseTrait, RequestTrait;

    /**
     *
     */
    public function __construct()
    {
        $this->setRequest();
    }

    /**
     * @return array<string, string>
     */
    public function actions(): array
    {
        return [];
    }
}