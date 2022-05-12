<?php

namespace GSpataro\Routing;

abstract class Controller
{
    /**
     * Initialize controller
     *
     * @param Request $request
     * @param Response $response
     */

    public function __construct(
        protected Request $request,
        protected Response $response
    ) {
    }
}
