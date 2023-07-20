<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Actions;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Vozimsan\Core\Rest\Http\Request;
use Vozimsan\Core\Rest\Http\Traits\JsonResponseTrait;

abstract class AbstractBaseAction
{
    use JsonResponseTrait;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     *
     */
    public function __construct()
    {
        $requestJson = json_decode(file_get_contents('php://input'), true) ?? [];

        $this->request = new Request(
            $_GET,
            $_POST,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER,
            $requestJson
        );
    }

    /**
     * @return mixed
     */
    abstract public function __invoke(): Response;
}