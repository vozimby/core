<?php
declare(strict_types=1);

namespace Vozimsan\Core\Rest\Http;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->get($name) ?? null;
    }

    /**
     * @param array $names
     * @return array
     */
    public function only(array $names): array
    {
        $data = [];
        foreach ($names as $name) {
            if (!is_null($this->get($name))) $data[$name] = $this->get($name);
        }

        return $data;
    }

    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
     * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
     * public property instead (attributes, query, request).
     *
     * Order of precedence: PATH (routing placeholders or custom attributes), GET, POST
     *
     * @internal use explicit input sources instead
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this !== $result = $this->attributes->get($key, $this)) {
            return $result;
        }

        if ($this->query->has($key)) {
            return $this->query->all()[$key];
        }

        if ($this->request->has($key)) {
            return $this->request->all()[$key];
        }

        if (isset($this->content[$key])) {
            return $this->content[$key];
        }

        return $default;
    }
}