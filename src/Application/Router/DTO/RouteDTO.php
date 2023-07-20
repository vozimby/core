<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Router\DTO;

use Vozimsan\Core\Application\Actions\AbstractBaseAction;
use Vozimsan\Core\Application\Controllers\AbstractBaseController;

class RouteDTO
{
    /**
     * @param AbstractBaseController|AbstractBaseAction $controller
     * @param string $method
     * @param array $httpMethods
     */
    public function __construct(
        public AbstractBaseController|AbstractBaseAction $controller,
        public string $method,
        public array $httpMethods,
    )
    {
    }
}