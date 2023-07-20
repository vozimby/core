<?php
/**
 * @param string $name
 * @param mixed|null $default
 * @return string|int|null
 */
function envGet(string $name, mixed $default = null): int|string|null {
    return $_ENV[$name] ?? $default;
}