<?php

namespace GSpataro\Routing;

final class RoutesCollection
{
    /**
     * Store routes
     *
     * @var array
     */

    private array $routes = [];

    /**
     * Verify if the collection has a route
     *
     * @param string $tag
     * @return bool
     */

    public function has(string $tag): bool
    {
        return isset($this->routes[$tag]);
    }

    /**
     * Add a route to the collection
     *
     * @param string $tag
     * @param string $path
     * @param array $methods
     * @param array $callback
     * @param array $middlewares
     * @return void
     */

    public function add(string $tag, string $path, array $methods, array $callback, array $middlewares): void
    {
        if ($this->has($tag)) {
            throw new Exception\RouteFoundException(
                "Route named '{$tag}' already exists in the collection."
            );
        }

        $methodsValues = [];

        for ($i = 0; $i < count($methods); $i++) {
            if (!$methods[$i] instanceof Method) {
                throw new Exception\InvalidRouteMethodException(
                    "Invalid methods provided to route '{$tag}'. Use the Routing\Method enum to provide methods."
                );
            }

            $methodsValues[] = $methods[$i]->value;
        }

        if (
            !isset($callback[0]) ||
            !isset($callback[1]) ||
            !class_exists($callback[0]) ||
            !method_exists($callback[0], $callback[1])
        ) {
            throw new Exception\InvalidRouteCallbackException(
                "Invalid callback provided to route '{$tag}'."
            );
        }

        if (get_parent_class($callback[0]) != Controller::class) {
            throw new Exception\InvalidControllerException(
                "Invalid controller provided to route named '{$tag}'. A controller must extend the Routing\Controller abstract class."
            );
        }

        for ($i = 0; $i < count($middlewares); $i++) {
            if (!class_exists($middlewares[$i]) || get_parent_class($middlewares[$i]) != Middleware::class) {
                throw new Exception\InvalidRouteMiddlewareException(
                    "Invalid middleware '{$middlewares[$i]}' provided to route '{$tag}'."
                );
            }
        }

        $this->routes[$tag] = [
            "path" => $path,
            "methods" => $methodsValues,
            "middlewares" => $middlewares,
            "controller" => $callback[0],
            "method" => $callback[1]
        ];
    }

    /**
     * Add multiple routes at a time
     *
     * @param array $routes
     * @return void
     */

    public function feed(array $routes): void
    {
        foreach ($routes as $tag => $params) {
            if (!isset($params['path']) || !isset($params['callback'])) {
                throw new Exception\InvalidRouteParamsException(
                    "Incomplete route '{$tag}' definition. A route must include at least a path and a callback."
                );
            }

            $params['methods'] = $params['methods'] ?? [Method::GET];
            $params['middlewares'] = $params['middlewares'] ?? [];

            $this->add($tag, $params['path'], $params['methods'], $params['callback'], $params['middlewares']);
        }
    }

    /**
     * Get a route from the collection
     *
     * @param string $tag
     * @return array
     */

    public function get(string $tag): array
    {
        if (!$this->has($tag)) {
            throw new Exception\RouteNotFoundException(
                "Route named '{$tag}' not found."
            );
        }

        return $this->routes[$tag];
    }

    /**
     * Get path to a route
     *
     * @param string $tag
     * @param array $params
     * @return string
     */

    public function getPath(string $tag, array $params = []): string
    {
        $route = $this->get($tag);
        $routePath = $route['path'];
        $regex = "/\/\{([a-z]+)\:([a-z|]+)\}/";

        if (preg_match($regex, $routePath)) {
            $routePath = preg_replace_callback($regex, function ($m) use ($params) {
                return isset($params[$m[1]]) && !is_null($params[$m[1]]) ? "/{$params[$m[1]]}" : null;
            }, $routePath);
        }

        return $routePath;
    }

    /**
     * Get all routes
     *
     * @return array
     */

    public function getAll(): array
    {
        return $this->routes;
    }
}
