<?php

namespace GSpataro\Routing;

final class Router
{
    /**
     * Initialize router
     *
     * @param RoutesCollection $routes
     * @param Request $request
     */

    public function __construct(
        private RoutesCollection $routes,
        private Request $request
    ) {
    }

    /**
     * Remove trailing slash from path
     *
     * @param string $path
     * @return string
     */

    public function removeTrailingSlash(string $path): string
    {
        $path = rtrim($path, "/");
        return strlen($path) == 0 ? "/" : $path;
    }

    /**
     * Prepare route path regex
     *
     * @param string $path
     * @return string
     */

    public function prepareRegex(string $path): string
    {
        $path = str_replace("/", "\/", $path);

        $path = preg_replace_callback("/\\\\\/\{([a-z]+)\:([a-z|]+)\}/", function ($m) {
            $types = explode("|", $m[2]);
            $regexStart = "\/";
            $regexGroup = "?P<{$m[1]}>";
            $regexContent = "";
            $regexEnd = "+";

            foreach ($types as $type) {
                switch ($type) {
                    case "string":
                        $regexContent .= "a-zA-Z";
                        break;
                    case "int":
                        $regexContent .= "0-9";
                        break;
                    case "chars":
                        $regexContent .= "-_.";
                        break;
                    case "misc":
                        $regexContent .= "a-zA-Z0-9-_.";
                        break;
                    case "null":
                        $regexStart = "[\/]?";
                        $regexEnd = "*";
                        break;
                }
            }

            return "{$regexStart}({$regexGroup}[{$regexContent}]{$regexEnd})";
        }, $path);

        return "/^{$path}$/";
    }

    /**
     * Start the routing process
     *
     * @param string $path
     * @return Response
     */

    public function deploy($requestPath = null): Response
    {
        $requestPath = $this->removeTrailingSlash($requestPath ?? $this->request->path);
        $requestMethod = $this->request->method;
        $matchingRoute = $this->routes->get("error404");
        $failed = true;

        foreach ($this->routes->getAll() as $routeName => $route) {
            $routePathRegex = $this->prepareRegex($this->removeTrailingSlash($route['path']));

            if (!preg_match($routePathRegex, $requestPath, $matches)) {
                continue;
            }

            $matchingRoute = $route;
            $matchingRouteName = $routeName;
            $failed = false;
            break;
        }

        $response = new Response($this->request, $this->routes, $matchingRoute);
        $response->setAllowMethodsHeader($matchingRoute['methods']);

        if ($failed) {
            $response->setStatusHeader(404);
        } elseif (!in_array($this->request->method, $matchingRoute['methods'])) {
            $response->setStatusHeader(405);
            $response->matchingRoute = $this->routes->get("error405");
        } else {
            $response->setStatusHeader(200);
        }

        $params = isset($matches) ? array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY) : [];
        $params = array_filter($params, function ($value) {
            return $value == "" ? null : $value;
        }, ARRAY_FILTER_USE_BOTH);

        if (!empty($matchingRoute['middlewares'])) {
            foreach ($matchingRoute['middlewares'] as $middleware) {
                $middleware = new $middleware($this->request);
                $middleware->process($params, $this);
            }
        }

        return $response;
    }
}
