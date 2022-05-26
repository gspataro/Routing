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
     * Store request input
     *
     * @var array
     */

    public readonly array $input;

    /**
     * Initialize request
     */

    public function __construct()
    {
        $this->protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off" ? "https" : "http";
        $this->domain = $_SERVER['SERVER_NAME'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->input = json_decode(file_get_contents("php://input"), true) ?? [];
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
     * Get variable from $_FILES
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
