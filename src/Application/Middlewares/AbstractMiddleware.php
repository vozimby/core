<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Middlewares;

use Symfony\Component\HttpFoundation\Response;
use Vozimsan\Core\Rest\Http\Request;
use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    use JsonResponseTrait;
}