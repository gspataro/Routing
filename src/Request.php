<?php

namespace GSpataro\Routing;

final class Request
{
    /**
     * Store request protocol
     *
     * @var string
     */

    public string $protocol;

    /**
     * Store request domain
     *
     * @var string
     */

    public string $domain;

    /**
     * Store request path
     *
     * @var string
     */

    public string $path;

    /**
     * Store request method
     *
     * @var string
     */

    public string $method;

    /**
     * Initialize request
     */

    public function __construct()
    {
        $this->protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off" ? "https" : "http";
        $this->domain = $_SERVER['SERVER_NAME'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get or set $_GET variable
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */

    public function get(string $key, mixed $value = null): mixed
    {
        if (!is_null($value)) {
            $_GET[$key] = $value;
        }

        return $_GET[$key] ?? null;
    }

    /**
     * Get $_POST variable
     *
     * @param string $key
     * @return mixed
     */

    public function post(string $key)
    {
        return $_POST[$key] ?? null;
    }

    /**
     * Get $_FILES variable
     *
     * @param string $key
     * @return mixed
     */

    public function files(string $key)
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Get or set $_SESSION variable
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */

    public function session(string $key, mixed $value = null): mixed
    {
        if (!is_null($value)) {
            $_SESSION[$key] = $value;
        }

        return $_SESSION[$key] ?? null;
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
     * Get $_SERVER variable
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
