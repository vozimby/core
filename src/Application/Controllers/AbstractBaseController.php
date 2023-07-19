<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Controllers;

abstract class AbstractBaseController
{
    /**
     * @return array<string, string>
     */
    public function actions(): array
    {
        return [];
    }
}