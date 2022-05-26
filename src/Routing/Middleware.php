<?php

namespace GSpataro\Routing;

abstract class Middleware
{
    /**
     * Initialize middleware
     *
     * @param Request $request
     * @param Response $response
     */

    public function __construct(
        protected Request $request,
        protected Response $response
    ) {
    }

    /**
     * Process the request
     *
     * @param array $params
     * @return void
     */

    abstract public function process(array $params = []): void;
}
