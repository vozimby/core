<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Http\Constants;

class StatusCode
{
    public const SUCCESS = 200;
    public const SUCCESS_CREATED = 201;
    public const VALIDATION_ERROR = 422;
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const NOT_FOUND = 404;
    public const NOT_ALLOWED = 403;
    public const SERVER_ERROR = 500;
    public const RATE_LIMIT = 429;

    public const METHOD_NOT_ALLOWED = 405;
}