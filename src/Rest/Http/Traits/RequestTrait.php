<?php
declare(strict_types=1);

namespace Vozimsan\Core\Rest\Http\Traits;

use Vozimsan\Core\Rest\Http\Request;

trait RequestTrait
{
    /**
     * @var Request
     */
    public Request $request;

    /**
     */
    public function setRequest(): void
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

}