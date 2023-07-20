<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Http\Enums;

enum HttpMethodEnums: string
{
    case GET = "GET";
    case POST = "POST";
    case PUT = "PUT";
    case DELETE = "DELETE";
    case OPTION = "OPTION";

/*    public function get(string $method): string
    {
        return match ($method) {
            HttpMethodEnums::GET->value => HttpMethodEnums::GET,
            HttpMethodEnums::POST->value => HttpMethodEnums::POST,
            HttpMethodEnums::PUT->value => HttpMethodEnums::PUT,
            HttpMethodEnums::DELETE->value => HttpMethodEnums::DELETE,
            HttpMethodEnums::OPTION->value => HttpMethodEnums::OPTION,
            default => ""
        };
    }*/
}
