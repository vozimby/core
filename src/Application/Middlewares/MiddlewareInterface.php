<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Middlewares;

use Symfony\Component\HttpFoundation\Response;
use Vozimsan\Core\Rest\Http\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @return Response|false
     */
    public function process(Request $request): Response|false;
}