<?php

namespace GSpataro\Routing;

final class Response
{
    /**
     * Initialize response
     *
     * @param Request $request
     * @param RoutesCollection $routes
     * @param array $matchingRoute
     */

    public function __construct(
        private Request $request,
        private RoutesCollection $routes,
        public array $matchingRoute
    ) {
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    /**
     * Set response status header
     *
     * @param int $status
     * @return void
     */

    public function setStatusHeader(int $status): void
    {
        $type = match($status) {
            200 => "200 OK",
            404 => "404 Not Found",
            405 => "405 Method Not Allowed",
            default => "200 OK"
        };

        header("{$this->request->server('server_protocol')} {$type}");
    }

    /**
     * Set response Access-Control-Allow-Methods header
     *
     * @param array $allowedMethods
     * @return void
     */

    public function setAllowMethodsHeader(array $allowedMethods): void
    {
        $implodedAllowedMethods = implode(", ", $allowedMethods);
        header("Access-Control-Allow-Methods: {$implodedAllowedMethods}");
    }

    /**
     * Set response Content-Type header
     *
     * @param string $type
     * @return void
     */

    public function setContentTypeHeader(string $type): void
    {
        header("Content-Type: {$type}; charset=UTF-8");
    }

    /**
     * Get the URL to a route
     *
     * @param string $routeTag
     * @param array $params
     * @param array $query
     * @return string
     */

    public function urlTo(string $routeTag, array $params = [], ?array $query = []): string
    {
        $url = "{$this->request->protocol}://{$this->request->domain}";
        $routePath = $this->routes->getPath($routeTag, $params);
        $queryString = is_null($query) || empty($query) ? null : "?" . http_build_query($query);

        return "{$url}{$routePath}{$queryString}";
    }

    /**
     * Redirect the user to another location
     *
     * @param string $location
     * @param array $params
     * @return void
     */

    public function redirect(string $location, array $params = []): void
    {
        if (!$this->routes->has($location)) {
            header("location: {$location}");
        } else {
            header("location: {$this->urlTo($location, $params)}");
        }
    }

    /**
     * Refresh the page
     *
     * @return void
     */

    public function refresh(): void
    {
        header("Refresh:0");
    }

    /**
     * Redirect to the previous page
     *
     * @return void
     */

    public function goBack(): void
    {
        $this->redirect($_SERVER['HTTP_REFERER'] ?? "javascript://history.go(-1)");
    }
}
