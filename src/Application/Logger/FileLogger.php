<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Logger;

use Psr\Log\AbstractLogger;
use Vozimsan\Core\Application\App;

class FileLogger extends AbstractLogger
{

    /**
     * @param $level
     * @param \Stringable|string $message
     * @param array $context
     * @return void
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $path = App::$basePath."/logs/";

        if (!is_dir($path)) {
            mkdir($path);
        }

        $body = "[".date('Y-m-d H:i:s')."]: $message\r\n";
        file_put_contents("$path/$level.log", $body, FILE_APPEND);
    }
}