<?php
declare(strict_types=1);

namespace Vozimsan\Core\Application\Helpers;

class DirectoryHelper
{
    /**
     * @param string $dir
     * @param string $namespace
     * @return string[]
     */
    public function getNamespaces(string $dir, string $namespace = ''): array
    {
        $dir = rtrim($dir, '\\/');
        $result = [];

        foreach (scandir($dir) as $f) {
            if ($f !== '.' and $f !== '..') {
                if (is_dir("$dir/$f")) {
                    $result = array_merge($result, $this->getNamespaces("$dir/$f", "$namespace$f\\"));
                } else {
                    // Проверка, что файл имеет постфикс "Controller"
                    if (str_ends_with($f, 'Controller.php')) {
                        $result[] = "App\\" . $namespace . substr($f, 0, -4);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $dir
     * @param string $prefix
     * @return string[]
     */
    public function getControllers(string $dir, string $prefix = ''): array
    {
        $dir = rtrim($dir, '\\/');
        $result = [];

        foreach (scandir($dir) as $f) {
            if ($f !== '.' and $f !== '..') {
                if (is_dir("$dir/$f")) {
                    $result = array_merge($result, $this->getControllers("$dir/$f", "$prefix$f\\"));
                } else {
                    if (str_ends_with($f, 'Controller.php')) {
                        $result[] = 'app\\'.$prefix . $f;
                    }
                }
            }
        }

        return $result;
    }
}