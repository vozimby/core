<?php
declare(strict_types=1);

namespace Vozimsan\Core\Rest\Http\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Vozimsan\Core\Application\Http\Constants\StatusCode;

trait JsonResponseTrait
{
    /**
     * вызываем когда нам надо отдать успешный ответ
     *
     * @param array|object $data - наши данные, которые будут экранироваться в JSON
     * @param int $statusCode - статус-код. по дефолут - 200
     * @param array $headers
     * @return Response
     */
    protected function success(array|object $data, int $statusCode = StatusCode::SUCCESS, array $headers = []): Response
    {
        $responseHeaders = array_merge($this->getHeaders(), $headers);

        return new JsonResponse($data, $statusCode, $responseHeaders);
    }

    /**
     * вызываем, когда нам надо отдать ошибку. отличается от успешного ответа тем, что мы просто передаём текст ошибки
     *
     * @param string $message - текст ошибки
     * @param int $statusCode - статус-код. по дефолту - 200
     * @param array $data - дополнительная информация
     * @param array $headers
     * @return Response
     */
    protected function error(string $message, int $statusCode = StatusCode::SUCCESS, array $data = [], array $headers = []): Response
    {
        $response = ['code' => $statusCode, 'message' => $message];
        if (count($data) > 0) {
            $response['data'] = $data;
        }

        $responseHeaders = array_merge($this->getHeaders(), $headers);

        return new JsonResponse($response, $statusCode, $responseHeaders);
    }

    /**
     * этот метод выдаёт контент-тайп JSON и статус-код
     *
     * @param int $statusCode - статус-код, по дефолту 200
     * @return void
     */
    protected function setHeaders(int $statusCode = StatusCode::SUCCESS): void
    {
        header("Content-Type: application/json");
        header("Accept: application/json");
        http_response_code($statusCode);
    }

    /**
     * @return void
     */
    protected function setCors(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type');
    }

    /**
     * @param bool $withCors
     * @return string[]
     */
    protected function getHeaders(bool $withCors = false): array
    {
        $headers = ['Content-Type'=>'application/json', 'Accept' => 'application/json'];
        if ($withCors) {
            $headers = array_merge($headers, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type'
            ]);
        }
        return $headers;
    }
}