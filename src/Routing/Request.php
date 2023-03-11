<?php

namespace GSpataro\Routing;

final class Request
{
    /**
     * Store request protocol
     *
     * @var string
     */

    public readonly string $protocol;

    /**
     * Store request domain
     *
     * @var string
     */

    public readonly string $domain;

    /**
     * Store request path
     *
     * @var string
     */

    public readonly string $path;

    /**
     * Store request method
     *
     * @var string
     */

    public readonly string $method;

    /**
     * Store request input
     *
     * @var array
     */

    public readonly array $input;

    /**
     * Initialize request
     *
     * @param array $parameters
     */

    public function __construct(array $parameters = [])
    {
        $this->protocol = $parameters['https'] ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http');
        $this->domain = $parameters['serverName'] ?? $_SERVER['SERVER_NAME'] ?? '';
        $this->path = $parameters['requestUri'] ?? $_SERVER['REQUEST_URI'] ?? '';
        $this->method = $parameters['method'] ?? $_SERVER['REQUEST_METHOD'] ?? Method::GET->value;
        $this->input = $parameters['input'] ?? (json_decode(file_get_contents('php://input'), true) ?? []);
    }

    /**
     * Get variable from $_GET array
     *
     * @param string $key
     * @return mixed
     */

    public function get(string $key): mixed
    {
        return $_GET[$key] ?? null;
    }

    /**
     * Get variable from $_POST array
     *
     * @param string $key
     * @return mixed
     */

    public function post(string $key)
    {
        return $_POST[$key] ?? null;
    }

    /**
     * Get variable from request input
     *
     * @param string $key
     * @return mixed
     */

    public function input(string $key): mixed
    {
        return $this->input[$key] ?? null;
    }

    /**
     * Get variable from $_FILES array
     *
     * @param string $key
     * @return mixed
     */

    public function files(string $key)
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Get variable from $_SESSION array
     *
     * @param string $key
     * @return mixed
     */

    public function getSession(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Set variable into the $_SESSION array
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */

    public function setSession(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Delete $_SESSION variable
     *
     * @param string $key
     * @return void
     */

    public function deleteSession(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Get variable from $_SERVER array
     *
     * @param string $key
     * @return mixed
     */

    public function server(string $key): mixed
    {
        $key = strtoupper($key);
        return $_SERVER[$key] ?? null;
    }
}
