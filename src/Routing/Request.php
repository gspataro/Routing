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
     * Store request GET
     *
     * @var array
     */

    private readonly array $get;

    /**
     * Store request POST
     *
     * @var array
     */

    private readonly array $post;

    /**
     * Store request FILES
     *
     * @var array
     */

    private readonly array $files;

    /**
     * Store request SESSION
     *
     * @var array
     */

    private array $session;

    /**
     * Store request SERVER
     *
     * @var array
     */

    private readonly array $server;

    /**
     * Initialize request
     *
     * @param string|null $protocol
     * @param string|null $domain
     * @param string|null $path
     * @param string|null $method
     * @param array|null $input
     * @param array|null $get
     * @param array|null $post
     * @param array|null $files
     * @param array|null $session
     * @param array|null $server
     */

    public function __construct(
        ?string $protocol = null,
        ?string $domain = null,
        ?string $path = null,
        ?string $method = null,
        ?array $input = null,
        ?array $get = null,
        ?array $post = null,
        ?array $files = null,
        ?array $session = null,
        ?array $server = null
    ) {
        $this->protocol = $protocol ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http');
        $this->domain = $domain ?? $_SERVER['SERVER_NAME'] ?? '';
        $this->path = $path ?? $_SERVER['REQUEST_URI'] ?? '';
        $this->method = $method ?? $_SERVER['REQUEST_METHOD'] ?? Method::GET->value;
        $this->input = $input ?? (json_decode(file_get_contents('php://input'), true) ?? []);
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->files = $files ?? $_FILES;
        $this->session = $session ?? (session_status() == PHP_SESSION_ACTIVE ? $_SESSION : []);
        $this->server = $server ?? $_SERVER;
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
     * Verify if session is started or throw an exception
     *
     * @return void
     */

    private function verifySessionStatus(): void
    {
        if (session_status() == PHP_SESSION_DISABLED) {
            throw new Exception\SessionNotStartedException(
                "You cannot use sessions. Sessions are disabled, please check your php.ini configuration."
            );
        }

        if (session_status() == PHP_SESSION_NONE) {
            throw new Exception\SessionNotStartedException(
                "You cannot use sessions. Please put you session_start() before the instance of Request."
            );
        }
    }

    /**
     * Get variable from $_SESSION array
     *
     * @param string $key
     * @return mixed
     */

    public function getSession(string $key): mixed
    {
        $this->verifySessionStatus();
        return $this->session[$key] ?? null;
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
        $this->verifySessionStatus();
        $this->session[$key] = $value;
    }

    /**
     * Delete $_SESSION variable
     *
     * @param string $key
     * @return void
     */

    public function deleteSession(string $key): void
    {
        $this->verifySessionStatus();

        if (isset($this->session[$key])) {
            unset($this->session[$key]);
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
