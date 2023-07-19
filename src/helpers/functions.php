<?php
/**
 * @param string $name
 * @param mixed|null $default
 * @return string|int|null
 */
function envget(string $name, mixed $default = null) {
    return $_ENV[$name] ?? $default;
}