<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Actions;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Vozimsan\Core\Rest\Http\Request;
use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;
use Vozimsan\Core\Rest\Http\Traits\RequestTrait;

abstract class AbstractBaseAction
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
     * @return mixed
     */
    abstract public function __invoke(): Response;
}